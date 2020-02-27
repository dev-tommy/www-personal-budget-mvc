function refreshCategoriesAndMethods(elementSource) {
    var url = "";
    var select;
    switch (elementSource) {
        case 'income':
            url = "/get-all-incomes-categories";
            select = $('#incomeCategory');
            break;
        case 'expense':
            url = "/get-all-expenses-categories";
            select = $('#expenseCategory');
            break;
        case 'payment':
            url = "/get-all-payments-methods";
            select = $('#paymentMethod');
            break;
        default:
            return;
    }
    $.post(
        url,
        function (answer) {
            select.empty();
            var categories = JSON.parse(answer);
            $.each(categories, function (i, categorie) {
                select
                    .append($("<option/>")
                        .attr("value", categorie["id"])
                        .text(categorie["name"])
                    );

                if (!isNaN(categorie["expense_limit"]) && categorie["expense_limit"] != null) {
                    select
                        .append($("<option/>")
                            .attr("id", "limit" + categorie['id'])
                            .attr("value", categorie["id"])
                            .attr("limit", categorie["expense_limit"])
                            .attr("class", "")
                            .attr("disabled", true)
                            .addClass("text-danger")
                            .css({
                                "font-style": "italic",
                                "font-style": "bold",
                                "font-size": "0.6em"
                            })
                            .html('  &nbsp;&nbsp; &#8627; Limit: ' + categorie["expense_limit"] + ' [zł/m-c]')
                        );
                }
            });
        }
    );
}

function getIncomesCategoriesHints() {
    var categoryName = $('#incomeCategoryName').val();
    var url = "/get-incomes-categories-hints";
    var hints = $("#incomeCategoryHints");
    if (categoryName != '') {
        $.post(
            url,
            {
                categoryName: categoryName
            },
            function (answer) {
                hints.empty();

                var categoryHints = JSON.parse(answer);
                $.each(categoryHints, function (i, hint) {
                    hints
                        .append($("<li/>")
                            .attr("class", "")
                            .addClass("hint")
                            .addClass("list-group-item")
                            .addClass("small")
                            .addClass("py-0")
                            .addClass("pl-5")
                            .text(hint['name'])
                        );
                });
            }
        );
    } else {
        hints.empty();
    }
}

function getExpensesCategoriesHints() {
    var categoryName = $('#expenseCategoryName').val();
    var url = "/get-expenses-categories-hints";
    var hints = $("#expenseCategoryHints");
    if (categoryName != '') {
        $.post(
            url,
            {
                categoryName: categoryName
            },
            function (answer) {
                hints.empty();

                var categoryHints = JSON.parse(answer);
                $.each(categoryHints, function (i, hint) {
                    hints
                        .append($("<li/>")
                            .attr("class", "")
                            .addClass("hint")
                            .addClass("list-group-item")
                            .addClass("small")
                            .addClass("py-0")
                            .addClass("pl-5")
                            .text(hint['name'])
                        );
                });
            }
        );
    } else {
        hints.empty();
    }
}

function getPaymentsMethodsHints() {
    var methodName = $('#paymentMethodName').val();
    var url = "/get-payments-methods-hints";
    var hints = $("#paymentMethodHints");
    if (methodName != '') {
        $.post(
            url,
            {
                methodName: methodName
            },
            function (answer) {
                hints.empty();

                var methodHints = JSON.parse(answer);
                $.each(methodHints, function (i, hint) {
                    hints
                        .append($("<li/>")
                            .attr("class", "")
                            .addClass("hint")
                            .addClass("list-group-item")
                            .addClass("small")
                            .addClass("py-0")
                            .addClass("pl-5")
                            .text(hint['name'])
                        );
                });
            }
        );
    } else {
        hints.empty();
    }
}

function elementAdd(elementSource, elementName) {
    elementLimit = $.trim($("#addLimitValue").val());
    $.post(
        "/element-add",
        {
            source: elementSource,
            name: elementName,
            limit: elementLimit
        },
        function (answer, status) {
            if (status == "success") {
                $("#success").find(".modal-body").html(answer);
                $("#success").modal();
            } else {
                $("#fail").modal();
            }
        }
    );
}

function elementRemove(elementSource, elementId) {
    $.post(
        "/element-remove",
        {
            source: elementSource,
            id: elementId
        },
        function (answer, status) {
            if (status == "success") {
                $("#success").find(".modal-body").html(answer);
                $("#success").modal();
            } else {
                $("#fail").modal();
            }
        }
    );
}

function elementEdit(elementSource, elementId) {
    elementName = $.trim($("#askEditModalBody").val());
    elementLimit = $.trim($("#editLimitValue").val());
    limitCheckbox = $("#editLimit").prop("checked");
    $.post(
        "/element-edit",
        {
            source: elementSource,
            id: elementId,
            name: elementName,
            limit: elementLimit,
            limitCheckbox: limitCheckbox
        },
        function (answer, status) {
            if (status == "success") {
                $("#success").find(".modal-body").html(answer);
                $("#success").modal();
            } else {
                $("#fail").modal();
            }
        }
    );
}

$(document).ready(function () {
    var elementSource = "";
    var elementName = "";
    var elementId = 0;
    var elementLimit = 0.0;

    $('#incomeCategoryName').keyup(getIncomesCategoriesHints);

    $('#expenseCategoryName').keyup(getExpensesCategoriesHints);

    $('#paymentMethodName').keyup(getPaymentsMethodsHints);

    $('#askAdd').on('show.bs.modal', function () {
        if (elementSource == "expense") {
            $('[limit-show]').removeClass("d-none");
        }
    });
    $('#askAdd').on('hidden.bs.modal', function () {
        $('[limit-show]').addClass("d-none");

        refreshCategoriesAndMethods(elementSource);
    });

    $('#askEdit').on('show.bs.modal', function () {
        if (elementSource == "expense") {
            $('[limit-show]').removeClass("d-none");
            $("#editLimit").prop("checked", false);
        }
    });

    $('#askEdit').on('hidden.bs.modal', function () {
        $('[limit-show]').addClass("d-none");
        refreshCategoriesAndMethods(elementSource);
    });

    $('#askDelete').on('hidden.bs.modal', function () {
        refreshCategoriesAndMethods(elementSource);
    });

    $("#incomeCategoryAdd").click(function () {
        elementSource = "income";
        $("#askAddModalBody").val($.trim($("#incomeCategoryName").val()));
        elementName = $.trim($("#incomeCategoryName").val());
        $("#askAdd").modal();
    });
    $("#incomeCategoryRemove").click(function () {
        elementSource = "income";
        $("#askDeleteModalBody").val($.trim($("#incomeCategory option:selected").text()));
        elementId = $("#incomeCategory option:selected").val();
        $("#askDelete").modal();
    });
    $("#incomeCategoryEdit").click(function () {
        elementSource = "income";
        $("#askEditModalBody").val($.trim($("#incomeCategory option:selected").text()));
        elementId = $("#incomeCategory option:selected").val();
        $('#askEdit').on('shown.bs.modal', function () {
            $('#askEditModalBody').focus();
        })
        $("#askEdit").modal();
    });

    $("#expenseCategoryAdd").click(function () {
        elementSource = "expense";
        $("#askAddModalBody").val($.trim($("#expenseCategoryName").val()));
        elementName = $.trim($("#expenseCategoryName").val());
        $("#askAdd").modal();

    });
    $("#expenseCategoryRemove").click(function () {
        elementSource = "expense";
        $("#askDeleteModalBody").val($.trim($("#expenseCategory option:selected").text()));
        elementId = $("#expenseCategory option:selected").val();
        $("#askDelete").modal();
    });
    $("#expenseCategoryEdit").click(function () {
        elementSource = "expense";
        $("#askEditModalBody").val($.trim($("#expenseCategory option:selected").text()));
        elementId = $("#expenseCategory option:selected").val();
        $("#editLimitValue").val($("#limit" + elementId).attr("Limit"));
        $('#askEdit').on('shown.bs.modal', function () {
            $('#askEditModalBody').focus();
        })
        $("#askEdit").modal();
    });

    $("#paymentMethodAdd").click(function () {
        elementSource = "payment";
        $("#askAddModalBody").val($.trim($("#paymentMethodName").val()));
        elementName = $.trim($("#paymentMethodName").val());
        $("#askAdd").modal();
    });
    $("#paymentMethodRemove").click(function () {
        elementSource = "payment";
        $("#askDeleteModalBody").val($.trim($("#paymentMethod option:selected").text()));
        elementId = $("#paymentMethod option:selected").val();
        $("#askDelete").modal();
    });
    $("#paymentMethodEdit").click(function () {
        elementSource = "payment";
        $("#askEditModalBody").val($.trim($("#paymentMethod option:selected").text()));
        elementId = $("#paymentMethod option:selected").val();
        $('#askEdit').on('shown.bs.modal', function () {
            $('#askEditModalBody').focus();
        })
        $("#askEdit").modal();
    });

    $("#userNameEdit").click(function () {
        elementSource = "user";
        elementId = 1;
        $("#askEditModalBody").val($.trim($("#userName").val()));
        $('#askEdit').on('shown.bs.modal', function () {
            $('#askEditModalBody').focus();
        })
        $("#askEdit").modal();
    });

    $("#userEmailEdit").click(function () {
        elementSource = "user";
        elementId = 2;
        $("#askEditModalBody").val($.trim($("#userEmail").val()));
        $('#askEdit').on('shown.bs.modal', function () {
            $('#askEditModalBody').focus();
        })
        $("#askEdit").modal();
    });

    $("#userPasswordEdit").click(function () {
        elementSource = "user";
        elementId = 3;
        $("#askEditModalBody").val("");
        $('#askEdit').on('shown.bs.modal', function () {
            $('#askEditModalBody').focus();
        })
        $("#askEdit").modal();
    });

    $("#addElement").click(function () {
        elementAdd(elementSource, elementName);
    });

    $("#removeElement").click(function () {
        elementRemove(elementSource, elementId);
    });

    $("#editElement").click(function () {
        elementEdit(elementSource, elementId);
    });

    $("#addLimitValue").click(function () {
        $("#addLimit").prop('checked', true);
        $(this).removeAttr('readonly');
        $(this).focus();
    });

    $('#addLimit').click(function () {
        if ($(this).prop("checked") == true) {
            $("#addLimitValue").removeAttr('readonly');
            $("#addLimitValue").focus();
        }
        else if ($(this).prop("checked") == false) {
            $("#addLimitValue").attr('readonly');
        }
    });

    $("#editLimitValue").click(function () {
        $("#editLimit").prop('checked', true);
        $(this).removeAttr('readonly');
        $(this).focus();
    });

    $('#editLimit').click(function () {
        if ($(this).prop("checked") == true) {
            $("#editLimitValue").removeAttr('readonly');
            $("#editLimitValue").focus();
        }
        else if ($(this).prop("checked") == false) {
            $("#editLimitValue").attr('readonly');
        }
    });
});