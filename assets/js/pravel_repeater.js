jQuery(document).ready(function($){
jQuery.fn.extend({
    createRepeater: function (options = {}) {
		
        var hasOption = function (optionKey) {
            return options.hasOwnProperty(optionKey);
        };
        var option = function (optionKey) {
            return options[optionKey];
        };
        var addItem = function (items, key, itm_cls, fresh = true) {
            var itemContent = items;
            var group = itemContent.data("group");
            var item = itemContent;
            var input = item.find('input,select');
            input.each(function (index, el) {
                var attrName = $(el).data('name');
                var skipName = $(el).data('skip-name');
                if (skipName != true) {
                    $(el).attr("name", attrName + "[" + key + "]");
                } else {
                    if (attrName != 'undefined') {
                        $(el).attr("name", attrName);
                    }
                }
                if (fresh == true) {                   
                }
            })
            var itemClone = items;

            /* Handling remove btn */
            var removeButton = itemClone.find('.remove-btn');

            if (key == 0) {
              
            } else {
                removeButton.attr('disabled', false);
            }

            removeButton.attr('onclick', 'jQuery(this).parents(\'.'+itm_cls+'\').remove()');

            $("<tr class='"+itm_cls+"'>" + itemClone.html() + "<tr/>").appendTo(repeater);
        };
        /* find elements */
        var repeater = this;
        var items = repeater.find(".items");
        var key = 0;
        var addButton = repeater.find('.repeater-add-btn');
        var addButton2 = repeater.find('.repeater-add-block-btn');

        items.each(function (index, item) {
            items.remove();
            if (hasOption('showFirstItemToDefault') && option('showFirstItemToDefault') == true) {
                addItem($(item), key, 'items');
                key++;
            } else {
                if (items.length > 1) {
                    addItem($(item), key, 'items');
                    key++;
                }
            }
        });
		
		
		var price_repeater = this;
        var price_items = price_repeater.find(".custom_price_date");        
        var price_key = 0;
        var price_addButton = price_repeater.find('.repeater-add-btn_custom_price');
       

        price_items.each(function (index, item) {
            price_items.remove();
            if (hasOption('showFirstItemToDefault') && option('showFirstItemToDefault') == true) {
                addItem($(item), price_key, 'custom_price_date');
                price_key++;
            } else {
                if (price_items.length > 1) {
                    addItem($(item), price_key, 'custom_price_date');
                    price_key++;
                }
            }
        });
		
        price_addButton.on("click", function () {
            addItem($(price_items[0]), price_key, 'custom_price_date');
            price_key++;
        }); 
		

        addButton.on("click", function () {
            addItem($(items[0]), key, 'items');
            key++;
        });

        addButton2.on("click", function () {
            addItem($(items[0]), key, 'items');
            key++;
        });
    }
});
});