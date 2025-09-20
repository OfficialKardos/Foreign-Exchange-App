$(document).ready(function () {
    // Get currencies
    $.ajax({
        type: "GET",
        url: "../app/pages/api/currency-values",
        success: function (data) {
            console.log(data);
            buildCurrencies(data.data);
        },
        error: function (data) {
            let response_json = JSON.parse(data.responseText);
            Swal.fire(
                "Error",
                data.statusText + ": " + response_json.error_msg,
                "Error"
            );
        },
    });

    // Build currency dropdown
    function buildCurrencies(currencies) {
        let dropdownContainer = $("#currenciesDrop");
        let select = $("<select></select>")
            .attr("id", "currencySelect")
            .attr("name", "currency-select")
            .addClass("form-control");
        select.append(
            $("<option></option>")
                .attr("value", "")
                .text(DOMPurify.sanitize("Select Currency"))
        );

        currencies.forEach((currency) => {
            let value = DOMPurify.sanitize(currency.code);
            let label = `${DOMPurify.sanitize(currency.name)} (${DOMPurify.sanitize(
                currency.code
            )})`;

            select.append(
                $("<option></option>")
                    .attr("value", value)
                    .attr("data-id", DOMPurify.sanitize(currency.id))
                    .attr(
                        "data-surcharge",
                        DOMPurify.sanitize(currency.surcharge_percentage)
                    )
                    .attr("data-rate", DOMPurify.sanitize(currency.exchange_rate))
                    .text(label)
            );
        });

        dropdownContainer.append(
            $("<label></label>")
                .attr("for", "currencySelect")
                .text("Select Currency:")
        );
        dropdownContainer.append(select);

        select.on("change", calculateZAR);
    }

    // Build Foreign currency input
    let foreignContainer = $("#foreign_am");
    let foreignLabel = $("<label></label>")
        .attr("for", "foreign-amount")
        .text("Foreign Amount:");
    let foreignInput = $("<input>")
        .attr({
            type: "number",
            id: "foreign-amount",
            name: "foreign-amount",
            step: "0.01",
            placeholder: "Enter foreign amount",
        })
        .addClass("form-control");
    foreignContainer.append(foreignLabel, foreignInput);

    let surplusContainer = $("#surplus_am");
    let surchargeLabel = $("<p></p>").html(
        'Surcharge: <span id="surcharge">0.00</span>'
    );
    surplusContainer.append(surchargeLabel);

    // Build ZAR currency input
    let zarContainer = $("#zar_am");
    let zarLabel = $("<label></label>")
        .attr("for", "zar-amount")
        .text("ZAR Amount:");
    let zarInput = $("<input>")
        .attr({
            type: "number",
            id: "zar-amount",
            name: "zar-amount",
            step: "0.01",
            placeholder: "ZAR Amount",
        })
        .addClass("form-control")
        .prop("readonly", true);
    zarContainer.append(zarLabel, zarInput);

    // Do Calculations
    function calculateZAR() {
        let selectedOption = $("#currencySelect option:selected");
        let foreignVal = parseFloat(foreignInput.val()) || 0;

        if (!selectedOption.val() || foreignVal <= 0) {
            zarInput.val("");
            $("#surcharge").text("0.00");
            return;
        }

        let exchangeRate = parseFloat(selectedOption.data("rate")) || 0;
        let surcharge = parseFloat(selectedOption.data("surcharge")) || 0;
        let discount = parseFloat(selectedOption.data("discount")) || 0;

        // Calculate ZAR
        let amountZAR = foreignVal * exchangeRate;
        amountZAR += (amountZAR * surcharge) / 100;

        zarInput.val(amountZAR.toFixed(2));
        $("#surcharge").text(surcharge.toFixed(2) + "%");
    }

    foreignInput.on("input", calculateZAR);

    //Submit Form
    $("#currency-form").on("submit", function (e) {
        e.preventDefault();

        let selectedOption = $("#currencySelect option:selected");
        let selectedCurrency = selectedOption.val();
        let foreignVal = parseFloat($("#foreign-amount").val()) || 0;
        let zarVal = parseFloat($("#zar-amount").val()) || 0;
        let currencyId = parseFloat(selectedOption.data("id")) || 0;
        let exchangeRate = parseFloat(selectedOption.data("rate")) || 0;
        let surcharge = parseFloat(selectedOption.data("surcharge")) || 0;
        let surchargeAmount = (zarVal * surcharge) / 100;
        console.log(surchargeAmount)

        if (!selectedCurrency) {
            Swal.fire("Error", "Please select a currency.", "error");
            return;
        }
        if (foreignVal <= 0) {
            Swal.fire("Error", "Please enter a valid foreign amount.", "error");
            return;
        }

        $.ajax({
            type: "POST",
            url: "../app/pages/api/orders/",
            data: {
                foreign_amount: foreignVal,
                currency_code: selectedCurrency,
                zar_amount: zarVal,
                currency_id: currencyId,
                exchange_rate: exchangeRate,
                surcharge: surcharge,
                surcharge_amount: surchargeAmount,
            },
            beforeSend: function () {
                $("#loader").show();
                $("#overlay").show();
            },
            success: function (res) {
                console.log("Server response:", res);
                if (res.success) {
                    Swal.fire("Success", "Order placed successfully", "success").then(function () {
                        location.reload()
                    }).done;
                } else {
                    Swal.fire("Error", res.msg || "Unknown error occurred", "error");
                }
            },
            error: function (error) {
                let response_json = JSON.parse(error.responseText);
                Swal.fire(
                    "Error",
                    error.statusText + ": " + response_json.error_msg,
                    "error"
                );
            },
            complete: function () {
                $("#loader").hide();
                $("#overlay").hide();
            }
        });
    });
})
