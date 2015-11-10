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
                $(this).find(".placeholder").hide();
                $(ui.draggable).addClass("addedToFav").clone().appendTo(this);
            }
        }).sortable({
            items: "li:not(.placeholder)",
            sort: function() {
                $(this).removeClass("ui-state-default");
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
    });
    /* Hide Placeholder if it has Items or Show if it is empty */
    if ($(".header-favorites ul li:not(.placeholder)").length > 0) {
        $(".header-favorites ul li.placeholder").hide();
    } else {
        $(".header-favorites ul li.placeholder").show();
    }
    $("button#createPath").click(function() {
        var idsInOrder = $("#sortable2").sortable("toArray");
        var formData = new FormData();
        formData.append("flow", idsInOrder.join('_'));
        var request = new XMLHttpRequest();
        request.addEventListener("load", function() {
            $('div#output').html(this.responseText);
        });
        request.open("POST", "");
        request.send(formData);
    });
});
