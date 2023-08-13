$(document).ready(function () {
    $('.show-turnover').click(function () {
        var companyId = $(this).data('company-id');
        var modalBody = $('#turnoverModalBody');

        $.ajax({
            url: '/company/' + companyId + '/turnover',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                modalBody.empty();
                if (data.turnover.length !== 0) {
                    var table = '<table class="table">';
                    table += '<thead><tr><th>Year</th><th>Non-Current Assets</th><th>Current Assets</th><th>Equity Capital</th><th>Amounts payable and other liabilities</th><th>Sales revenue</th><th>Profit (loss) before taxes</th><th>Profit before taxes margin</th><th>Net profit (loss)</th><th>Net profit margin</th></tr></thead>';
                    table += '<tbody>';

                    for (var index in data.turnover) {
                        table += '<tr>';
                        table += '<td>' + data.turnover[index].year + '</td>';
                        table += '<td>' + data.turnover[index].non_current_assets + '</td>';
                        table += '<td>' + data.turnover[index].current_assets + '</td>';
                        table += '<td>' + data.turnover[index].equity_capital + '</td>';
                        table += '<td>' + data.turnover[index].amounts_payable_and_other_liabilities + '</td>';
                        table += '<td>' + data.turnover[index].sales_revenue + '</td>';
                        table += '<td>' + data.turnover[index].profit_loss_before_taxes + '</td>';
                        table += '<td>' + data.turnover[index].profit_before_taxes_margin + '</td>';
                        table += '<td>' + data.turnover[index].net_profit_loss + '</td>';
                        table += '<td>' + data.turnover[index].net_profit_margin + '</td>';
                        table += '</tr>';
                    }

                    table += '</tbody></table>';
                    modalBody.append(table);
                } else {
                    modalBody.append('<p>No turnover information available.</p>');
                }
                $('#turnoverModal').modal('show');
            },
            error: function () {
                modalBody.empty().append('<p>Error fetching turnover information.</p>');
                $('#turnoverModal').modal('show');
            }
        });
    });

    $('.turnover-modal-close').click(function () {
        $('#turnoverModal').modal('hide');
    });
});