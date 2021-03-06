$(document).ready(function() {
    /* jQuery Droppable */
    $(function() {
        $(".mn-items .rp-draggable li").draggable({
            appendTo: "body",
            helper: "clone"
        });
        $(".header-favorites ul").droppable({
            activeClass: "ui-state-default",
            hoverClass: "ui-state-hover",
            accept: ":not(.ui-sortable-helper)",
            drop: function(event, ui) {
                var elementsAlreadyThere = $("#sortable2").sortable("toArray");

                var isFound = $.inArray(ui.draggable.attr('uuid'), elementsAlreadyThere) !== -1;
                if(!isFound) {
                    $(this).find(".placeholder").hide();
                    $(ui.draggable).addClass("addedToFav").clone().appendTo(this);
                    changeHandler();
                }
            }
        }).sortable({
            items: "li:not(.placeholder)",
            sort: function() {
                $(this).removeClass("ui-state-default");
            },
            update: function() {
                changeHandler();                
            }
        });
    });
    /* Click Star Icon to Add to Drop Here Container */
    $('ul.rp-draggable li .fa-star-o').click(function() {
        var $target = $(this).closest("li"),
            $dropedList = $(".h-droped-list"),
            id = $target.attr("uuid");
        if (!$target.hasClass("addedToFav")) {
            $target.addClass("addedToFav").clone().appendTo($dropedList);
            $dropedList.find(".placeholder").hide();
        } else {
            $dropedList.find("li").each(function(index, item) {
                var $elem = $(item);
                if ($elem.attr("uuid") == id) {
                    $elem.remove();
                    $target.removeClass("addedToFav");
                }
                if ($dropedList.children().length == 1) {
                    var $lastItem = $($dropedList.children()[0]);
                    $lastItem.hasClass("placeholder") && $lastItem.show();
                }
            })
        }
        changeHandler();
    });
    /* Click Close Icon to Remove from Drop Here Container */
    $("ul.h-droped-list").on('click', 'li .fa-star-o', function() {
        var $target = $(this).closest("li"),
            $catalog = $("#catalog ul"),
            id = $target.attr("uuid"),
            $dropList = $target.parent("ul");
        $target.remove();
        $catalog.find("li").each(function(index, item) {
            var $elem = $(item);
            if ($elem.attr("uuid") == id) $elem.removeClass("addedToFav");
        })
        if ($dropList.children().length == 1) {
            var $lastItem = $($dropList.children()[0]);
            $lastItem.hasClass("placeholder") && $lastItem.show();
        }
        changeHandler();
    });
    /* Hide Placeholder if it has Items or Show if it is empty */
    if ($(".header-favorites ul li:not(.placeholder)").length > 0) {
        $(".header-favorites ul li.placeholder").hide();
    } else {
        $(".header-favorites ul li.placeholder").show();
    }

    $("button#reset").click(function() {
        // XXX remove everything from list
        $("#sortable2").children('li:not(.placeholder)').each(function(index, item) {
            item.remove();
        });
        // remove addedToFav classes
        $('#catalog ul').children('li').each(function(index, item) {
            var $elem = $(item);
            $elem.removeClass('addedToFav');
        });

        $dropedList = $(".h-droped-list");
        $dropedList.find(".placeholder").show();
        sendFormPost('delete-all-table');
    });

//    $("button#loop").click(function() {
//        // XXX remove everything from list
//        $("#sortable2").children('li:not(.placeholder)').each(function(index, item) {
//            item.remove();
//        });
//        // remove addedToFav classes
//        $('#catalog ul').children('li').each(function(index, item) {
//            var $elem = $(item);
//            $elem.removeClass('addedToFav');
//        });

//        $dropedList = $(".h-droped-list");
//        $dropedList.find(".placeholder").show();
//        sendFormPost('loop');
//    });

    var changeHandler = function()
    {
        var idsInOrder = $("#sortable2").sortable("toArray");
        if(0 === idsInOrder.length) {
            sendFormPost('loop');
        } else {
            sendFormPost(idsInOrder.join('_'));
        }
    };

    var sendFormPost = function(flow) {
        var formData = new FormData();
        formData.append("flow", flow);
        var request = new XMLHttpRequest();
        request.addEventListener("load", function() {
            $('div#output').html(this.responseText);
        });
        request.open("POST", "");
        request.send(formData);
    };
});
