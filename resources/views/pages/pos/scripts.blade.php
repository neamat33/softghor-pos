<script>
    /**
     * *****************************************
     * Initialize on Page Load
     * *****************************************
     */
    var empty = '';
    $('body').addClass('sidebar-folded');
    $('#id_code').blur();

    var localData = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];

    function showList() {
        if (localData.length <= 0) {
            $("#tbody").html(empty);
        } else {
            localData.forEach((item, index) => {
                domPrepend(item, index);
            });
        }
    }

    showList();
    totalCalculate();

    var cartList = [];

    /**
     * *****************************************
     * Helper Functions
     * *****************************************
     */


    function empty_field_check(placeholder) {
        // console.log(typeof placeholder);
        if(typeof placeholder == NaN){
            placeholder = 0;
        }else if (placeholder == null) {
            placeholder = 0;
        } else if (placeholder.trim() == "") {
            placeholder = 0;
        }else if(placeholder=='null'){
            placeholder=0;
        }
        return placeholder;
    }

    function to_sub_unit(main_val, sub_val, related_by,has_sub_unit) {
        if(has_sub_unit=='true'){
            return (main_val * related_by) + sub_val;
        }
        return main_val;


    }

    function convert_to_main_and_sub(quantity,has_sub_unit, related_by) {
        var main_qty = 0;
        var main_qty_as_sub = 0;
        var sub_qty = 0;
        
        main_qty = parseInt(quantity);

        if (has_sub_unit=="true" && quantity != 0 && related_by != 0) {
            main_qty = parseInt(quantity / related_by);
            main_qty_as_sub = main_qty * related_by;
            sub_qty = quantity - main_qty_as_sub;
        }

        return {
            'main_qty': main_qty,
            'sub_qty': sub_qty
        };
    }

    function calculate_sub_total(main_qty, sub_qty, unit_price, related_by,has_sub_unit) {
        var sub_unit_price = 0;

        if (has_sub_unit=="true"&&related_by != 0) {
            sub_unit_price = parseFloat(unit_price / related_by);
        }
        var main_price = main_qty * unit_price;
        var sub_price = sub_qty * sub_unit_price;

        return parseFloat(main_price + sub_price).toFixed(2);
    }

    /**
     * *****************************************
     * Manage Addition and Removal from LocalStorage
     * *****************************************
     */


    function pExist(pid) {
        let ldata = localStorage.getItem('pos-items') ? JSON.parse(localStorage.getItem('pos-items')) : [];
        return ldata.some(function (el) {
            return el.id === pid
        });
    }

    function storedata(data) {
        if (localStorage.getItem('pos-items') != null) {
            cartList = JSON.parse(localStorage.getItem('pos-items'))
            cartList.push(data);
        } else {
            cartList.push(data);
        }
        localStorage.setItem('pos-items', JSON.stringify(cartList));
    }

    function addProductToCard(product) {
        storedata(product);
        var x = 0;
        domPrepend(product, x++);
        totalCalculate();
    }


    /**
     * *****************************************
     * Search Product
     * *****************************************
     */

    //Code Field Autocomlete
    $("#id_code").autocomplete({
          source: function (req, res) {
            let url = "{{ route('product-code-search') }}";
            $.get(url, {req: req.term}, (data) => {
              res($.map(data, function (item) {
                return {
                  id: item.id,
                  value: item.name+" - "+item.code,
                  price: item.price
                }
              })); // end res

            });
          },
          select: function (event, ui) {

            $(this).val(ui.item.value);
            $("#search_product_id").val(ui.item.id);
            let url = "{{ route('product.details', 'placeholder_id') }}".replace('placeholder_id', ui.item.id);
            $.get(url, (product) => {
                // check stock
                  if(product.stock <= 0) {
                    toastr.warning('This product is Stock out. Please Purchases the Product.');
                    return false;
                  }


                if (pExist(product.id) == true) {
                    // toastr.warning('Please Increase the quantity.');
                    var selector ='input.product_id[value="'+product.id+'"]';
                    var field = $(selector);
                    var main_qty = parseInt(empty_field_check(field.parents('tr').find('.main_qty').val()));
                    field.parents('tr').find('.main_qty').val(main_qty+1);
                    handle_change(field);
                } else {
                    addProductToCard(product);
                }
            });
            $(this).val('');
            return false;
          },
          response: function (event, ui) {
            if(ui.content.length == 1) {
              ui.item = ui.content[0];
              $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
              $(this).autocomplete('close');

            }
          },
          minLength: 0
     });


    $("#product_search").autocomplete({
          source: function (req, res) {
            let url = "{{ route('product-search') }}";
            $.get(url, {req: req.term}, (data) => {
              res($.map(data, function (item) {
                return {
                  id: item.id,
                  value: item.name+" "+item.code,
                  price: item.price
                }
              })); // end res

            });
          },
          select: function (event, ui) {

            $(this).val(ui.item.value);
            $("#search_product_id").val(ui.item.id);
            let url = "{{ route('product.details', 'placeholder_id') }}".replace('placeholder_id', ui.item.id);
            $.get(url, (product) => {
                console.log(product);
                // check stock
                  if(product.stock <= 0) {
                    toastr.warning('This product is Stock out. Please Purchases the Product.');
                    return false;
                  }


                if (pExist(product.id) == true) {
                    toastr.warning('Please Increase the quantity.');
                } else {
                    addProductToCard(product);
                }

            });

            $(this).val('');

            return false;
          },
          response: function (event, ui) {
            if(ui.content.length == 1) {
              ui.item = ui.content[0];
              $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
              $(this).autocomplete('close');

            }
          },
          minLength: 0
     });




    /**
     * *****************************************
     * Manage Cart items
     * *****************************************
     */



     $(document).on('click', '.product', function () {
        let productId = $(this).attr('data-value');
        let url = "{{ route('product.details', 'placeholder_id') }}".replace('placeholder_id', productId);
        $.get(url, product => {
            // check stock
            if (product.stock <= 0) {
                toastr.warning('This product is Stock out. Please Purchases the Product.');
                return false;
            }

            if (pExist(product.id) == true) {
                toastr.warning('Please Increase the quantity.');
            } else {
                addProductToCard(product);
            }
        }); // Load Data to cart

    });


    $(document).on('click', '.remove-btn', function () {
        let itemIndex = $(this).attr('data-value');
        localData.splice(itemIndex, 1);
        localStorage.removeItem('pos-items');
        localStorage.setItem('pos-items', JSON.stringify(localData))
        $(this).parents('tr').remove();
        totalCalculate();
    });

    $("#clearList").on('click', function () {
        localStorage.removeItem('pos-items');
        $("#tbody").html(empty);
        totalCalculate();
    });



    function domPrepend(product = null, index = null) {
        var name = product.name;
        var quantity_data = '';

        if (product.sub_unit == null) {
            // alert("NO SUB UNIT");
            quantity_data =
                `<input type="text" class="has_sub_unit" hidden value="false">
                    <label class="ml-2 mr-2">${product.main_unit.name}:</label>
                    <input type="number" value="1" class="form-control col main_qty" name="main_qty[${product.id}]" data-value="${product.stock}" data-related="${product.main_unit.related_by}" onkeydown="return event.keyCode !== 190" min="0">`;
        } else {
            // alert("SUB UNIT");
            quantity_data =
                `<input type="text" class="has_sub_unit" hidden value="true">
                    <input type="text" class="conversion" hidden value="${product.main_unit.related_by}">
                    <label class="mr-1 ml-1">${product.main_unit.name}:</label>
                    <input type="number" value="1" class="form-control col main_qty mr-1" name="main_qty[${product.id}]" data-value="${product.stock}" data-related="${product.main_unit.related_by}" onkeydown="return event.keyCode !== 190" min="0">
                    <label class="mr-1">${product.sub_unit.name}:</label>
                    <input type="number" value="" class="form-control col sub_qty mr-1" name="sub_qty[${product.id}]"  onkeydown="return event.keyCode !== 190" min="0" max="${product.main_unit.related_by-1}">`;
        }

        let dom = `
              <tr>
                <td>
                  ${product.name + " - " + product.code}
                  <input type="hidden" class="name" value="${name.replace(/[&\/\\#,+()$~%.'":*?<>{}]/g, '')}" name="name[${product.id}]" />
                  <input type="hidden" value="${product.id}" name="product_id[${product.id}]" />
                </td>
                <td style="width:100px">
                    <div class="form-row">
                        ${quantity_data}
                    </div>
                </td>
                <td style="width:100px">
                  <input type="text" value="${product.price}" class="form-control rate" name="rate[${product.id}]" />
                </td>
                <td style="width:150px">
                  <input type="text" readonly name="sub_total[${product.id}]" class="form-control sub_total" value="${product.price}"/>
                </td>
                <td>
                  <a href="#" class="remove-btn item-index" data-value="${index}"><i class="fa fa-trash"></i></a>
                </td>
              </tr>
         `;
        $("#tbody").prepend(dom);
    }



    function handle_change(obj) {
        var main_val = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').val()));
        var sub_val = parseInt(empty_field_check(obj.parents('tr').find('.sub_qty').val()));
        let related_by = parseInt(empty_field_check(obj.parents('tr').find('.main_qty').attr('data-related')));
        var has_sub_unit = obj.parents('tr').find('.has_sub_unit').val();
        let converted_sub = to_sub_unit(main_val, sub_val, related_by ,has_sub_unit);
        // alert(has_sub_unit);
        let stock = obj.parents('tr').find('.main_qty').attr('data-value');


        if (stock < converted_sub) {
            // put the max stock
            var converted;
            if (has_sub_unit == "true") {
                converted = convert_to_main_and_sub(stock, has_sub_unit, related_by);
                obj.parents('tr').find('.main_qty').val(converted.main_qty);
                obj.parents('tr').find('.sub_qty').val(converted.sub_qty);
            } else {
                converted = convert_to_main_and_sub(stock,has_sub_unit, related_by);
                obj.parents('tr').find('.main_qty').val(converted.main_qty);
            }

            let price = obj.parents('tr').find('.rate').val();
            price = parseFloat(price);

            let subTotal = calculate_sub_total(converted.main_qty, converted.sub_qty, price, related_by,has_sub_unit);

            obj.parents('tr').find('.sub_total').val(subTotal);
            totalCalculate();

            toastr.warning('Not Enough Stock.');
        }else{
            let price = obj.parents('tr').find('.rate').val();
            price = parseFloat(price);
            let subTotal = calculate_sub_total(main_val, sub_val, price, related_by,has_sub_unit);
            obj.parents('tr').find('.sub_total').val(subTotal);
            totalCalculate();
        }
    }

    // main_qty
    $(document).on('change', '.main_qty', function(e) {
        handle_change($(this));
    });

    //sub_qty change
    $(document).on('change', '.sub_qty', function(e) {
        handle_change($(this));
    });

    // rate change
    $(document).on('change', '.rate', function(e) {
        handle_change($(this));
        return;
    });


    function totalCalculate() {
        let subTotalList = document.querySelectorAll('.sub_total');
        let qtyList = document.querySelectorAll('.qty');
        let total = 0;
        let totalQty = 0;
        $.each(subTotalList, (index, value) => {
            total += parseFloat(value.value);
        });
        $("#totalAmount").text(total);
        $("#receivable").text(total);
        // $("#after_discount").text(total);
        $("#receivable_input").val(total);

        $.each(qtyList, (index, value) => {
            totalQty += parseInt(value.value);
        });
        $("#totalQty").text(totalQty);
        $("#items").text(totalQty);

        calculate_total_receivable();
    }



    /**
     * *****************************************
     * Other Calculations - discount ETC
     * *****************************************
     */


    function calculate_total_receivable() {
        let discount = $("#discount").val();
        discount = empty_field_check(discount);


        let discountAmount = 0;
        if ((typeof discount === 'string' || discount instanceof String) && discount.includes("%")) {
            let removed_percent_discount = discount.replace('%', '');
            discount = parseFloat(removed_percent_discount);
            discountAmount = Math.round($("#receivable").text() * (discount / 100));
        } else {
            discountAmount = parseFloat(discount);
        }

        let totalAmount = parseFloat($("#receivable").text()) - discountAmount;

        $("#after_discount").text(totalAmount);
        $("#receivable_input").val($("#receivable").text() - discountAmount);
        $("#receivable_input").val(totalAmount);
        $("#discount_amount").val(discountAmount);

        update_balance();
    }


    $("#discount").on('keyup', function() {
        calculate_total_receivable();
    });

    // Delivery Cost etc add here

    // $(document).on('keyup', '#delivery_cost', function() {
    //     calculate_total_receivable();
    // });

    /**
     * *****************************************
     * Balance & PAYMENT
     * *****************************************
     */

    function update_balance() {
        let pay_amount = empty_field_check($('#pay_amount').val());
        let aDiscount = $("#after_discount").text();
        $("#balance").text(pay_amount - aDiscount);
        $("#balance_input").val(pay_amount - aDiscount);
    }

    $("#pay_amount").keyup(update_balance);

    $("#pay_amount").bind('change', update_balance);

    $("#paid_btn").on('click', function() {
        var costing = empty_field_check($("#after_discount").text());
        $("#pay_amount").val(costing);
        $("#balance").text(0);
        $("#balance_input").val(0);
    });

    // If customer is walk-in customer.
    $("#order-btn").on('click', function(e) {
        let customerId = $("#customer").val();
        let due = $("#balance").text();
        if (customerId == 0 && due < 0) {
            e.preventDefault();
            toastr.warning('Walk-in Customer is do not support due. Please make Payment or Change Customer');
        } else if (due > 0) {
            e.preventDefault();
            toastr.warning('Over Payment Not Allowed.');
        } else {
            e.preventDefault();
            $(this).attr( 'disabled','disabled' );
            $(this).closest("form").submit();
        }
    });


    $(document).on('click', '#payment-btn', function () {
        if ($.trim($('.name').val()) == '') {
            toastr.warning('Add Some Products...');
            return;
        }

        $("#payment-modal").modal('show');
    });


    /**
     * *****************************************
     * Right Side product filter and pagination
     * *****************************************
     */

    $(document).on('click', '.pagination a', function(event) {
        event.preventDefault();
        var url=$(this).attr('href');

        let category = null;
        let code = null;
        let page=1;

        if ($(this).attr('href').split('page=')[1]) {
            page = $(this).attr('href').split('page=')[1].split('&')[0];

        }

        if ($(this).attr('href').split('category=')[1]) {
            category = $(this).attr('href').split('category=')[1].split('&')[0];

        }

        if ($(this).attr('href').split('code=')[1]) {
            code = $(this).attr('href').split('code=')[1].split('&')[0];

        }

        fetch_data(page, category, code);
    });

    $(document).on('submit', '.product-filter', function (e) {
        e.preventDefault();
        var code = $('.product-filter .code').val();
        filter_fetch_data(code);
    });

    function getProductsByCat(id) {
        $.ajax({
            url: "/back/pos-products?category=" + id,
            success: function (data) {
                $("#products").html(data);
                // console.log(data)
            },
            error: function () {
                alert('Error !');
            }
        });
    }


    function fetch_data(page, category = null, code = null) {
        let url="/back/pos-products?page=" + page;
        if (category != null) {
            url = url + "&category=" + category;
        }

        if (code != null) {
            url = url+ "&code=" + code;
        }

        $.ajax({
            url: url,
            success: function(data) {
                $("#products").html(data);
            },
            error: function() {
                alert('Error !');
            }
        });
    }

    function filter_fetch_data(code) {
        $.ajax({
            url: "/back/pos-products?code=" + code,
            success: function (data) {
                $("#products").html(data);
            },
            error: function () {
                alert('Error !');
            }
        });
    }



</script>
