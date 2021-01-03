window.result = "";
var change_id;
var search_ele;
var k = 0, flag = 1, total;
var s = new Array();
var a = new Array();
var c_id;
var range,price_id;
var cg = false;
var quantity_ele;
var country;

$(document).ready(function () {
    $(".serch").keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            search_ele = $(".serch").val();
            window.location = "search.html?ele=" + search_ele;

        }
    });
});

function country_change(){
    country=$("#country1").val();
   
 $.ajax({
        url: "js/country.php",
        data: ({country:country,status:1}),
        type: "POST",
        success: function (data) {
            // alert(data);
            location.reload();

        }
    });
}

function main() {
    cart_value();
    session_validate();
    country_selected();
}
function login() {
    uname = $("#uname").val();
    pass = $("#password").val();
    $.ajax({
        url: "js/login.php",
        data: ({user_name: uname, password: pass}),
        type: "POST",
        success: function (data) {
        }
    });
    window.location = "login.html"
}
function sign_up() {
    uname = $("#username").val();
    pass = $("#password").val();
    email = $("#email").val();
    $.ajax({
        url: "js/sign_up.php",
        data: ({user_name: uname, password: pass, email: email}),
        type: "POST",
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            alert("Registerd Successfully");
            window.location = "login.html"
        }
    });
}
function relocate() {
    window.location = "user_orders.html"
}
function session_validate() {
    $.ajax({
        url: "js/session_validate.php",
        type: "POST",
        success: function (data) {
            if (data) {
                $(".previous_orders").html("<input type='button' onclick='relocate()'value='Past Orders'>");
                $(".login_l").html("<input type='button' onclick='session_clear()'value='Log out'>");
                $(".sess").html("<a href='login.html'><i class='hd-dign'></i>" + data + "</a>");
                $(".login_hide").hide();
            }
        }
    });
}
function country_selected()
{
    
    $.ajax({
        url: "js/country.php",
        data: ({status:2}),
        type: "POST",
        success: function (data) {
       
             //   alert(data);
              
                $("#country1").val(data);
            
        }
    });
}
function session_clear() {

    $.ajax({
        url: "js/session_clear.php",
        type: "POST",
        success: function (data) {
        }
    });
    window.location = "login.html"
}
function checkbox_click(gender, data)
{
    status_ele = "#" + data;
    status = $(status_ele).prop('checked');
    if (status == "true")
    {
        for (i = 1; i <= 18; i++)
        {
            ido_ele = "#" + i;
            $(ido_ele).removeAttr('checked');
        }
        if (gender == 'M')
            window.location = "product-m.html?id=" + data;
        else if (gender == 'F')
            window.location = "product.html?id=" + data;
        else if (gender == 'K')
            window.location = "product-k.html?id=" + data;
    } else
    {
        if (gender == 'M')
            window.location = "product-m.html";
        else if (gender == 'F')
            window.location = "product.html";
        else if (gender == 'K')
            window.location = "product-k.html";
    }
}
function swap(data1, data2)
{
    src1 = $(data1).attr("src");
    src2 = $(data2).attr("src");
    $(data2).attr("src", src1);
}
function id_value()
{
    
    var query = window.location.search.substring(1);
    var id = query.split("=").pop().trim();
    
    $.ajax({
        url: "js/server.php",
        data: ({id_value: id}),

        type: "POST",
        success: function (data) {
            console.log(data);
            var obj = jQuery.parseJSON(data);
            $(".banner").attr("src", obj.IMAGE_URL);
            $(".side_image1").attr("src", obj.IMAGE_URL);
            $(".side_image2").attr("src", obj.IMAGE_URL3);
            $(".item_add").attr("href", "checkout.html?id=" + obj.ID);
            $(".bannert").attr("data-thumb", obj.IMAGE_URL);
            $(".side_image1t").attr("data-thumb", obj.IMAGE_URL);
            $(".side_image2t").attr("data-thumb", obj.IMAGE_URL3);
            var r = $(".side_image2t").attr("data-thumb");
            $(".banner_price").html("$" + obj.PRICE);
            $(".banner_description").html(obj.DESCRIPTION);
            $(".banner_name").html(obj.NAME);
        }
    });
}
function product_load(gender)
{
    

    var query = window.location.search.substring(1);
    var id = query.split("=").pop().trim();
    $.ajax({
        url: "js/product_display.php",
        data: ({id_value: id, G: gender}),
        type: "POST",
        success: function (data) {
            var oj = jQuery.parseJSON(data);
            var obj = oj;
               console.log(obj);
            if (obj.length > 9)
                t = 9;
            else
                t = obj.length;
            ido_ele = "#" + id;
            $(ido_ele).attr('checked', 'checked');
            for (i = 0; i < t; i++)
            {
                temp = $(".image" + (i + 1));
                temp1 = $(".image" + (i + 1) + "a");
                temp.attr("src", obj[i].IMAGE_URL);
                var price_ele = temp.closest(".home-product-main").find(".srch");
                var smart_ele = temp.closest(".home-product-main").find(".simagea");
                var hidden_ele = temp.closest(".home-product-main");
                $(hidden_ele).show();
                price_ele.html("<span> $" + obj[i].PRICE + "</span>");
                temp1.attr("href", "single.html?id=" + obj[i].ID);
                smart_ele.attr("href", "single.html?id=" + obj[i].ID);
                smart_ele.html(obj[i].NAME);
            }
        }
    });
}
function click_on(data, id)
{
    country=$("#country1").val();
    $.ajax({
        url: "js/delete_cart.php",
        data: ({id_value: id}),
        type: "POST",
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            if(country=="india")
            total = total - obj.PRICE_INDIA;
            if(country=="australia")
            total = total - obj.PRICE_AUSTRALIA;
            if(country=="england")
            total = total - obj.PRICE_ENGLAND;
            if(country=="u.s")
            total = total - obj.PRICE_US;
            $(".total").html(total);
        }
    });
    ele = ".cart-header" + data,
            $(ele).fadeOut('slow', function (c) {
        $(ele).remove();
        window.location = "checkout.html"
    });
}
function cart_value()
{
    total=0;
    gender = "i";
   
    $.ajax({
        url: "js/cart_display.php",
        data: ({G: gender}),
        type: "POST",
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            for (i = 0; i < obj.length; i++)
                total += obj[i].PRICE;
            $(".simpleCart_total").html('$' + total);
        }
    });
}
function cart()
{
    country=$("#country1").val();
    var query = window.location.search.substring(1);
    id = query.split("=").pop().trim();
    $.ajax({
        url: "js/cart.php",
        data: ({id_value: id}),
        type: "POST",
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            if (obj == "unsuccess")
                alert("Product is out of stock");
            if (obj == "succ")
            {
            }
            addtocart();
        }
    });
}
function addtocart()
{
    
    gender = "i";
    total = 0
    $.ajax({
        url: "js/cart_display.php",
        data: ({G: gender}),
        type: "POST",
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            count = obj.length;
            total=0;
            if (!count)
                count = 0;
            $(".count").append("(" + count + ")");
            for (i = 0; i < obj.length; i++)
            {
                total+=obj[i].PRICE;
                $(".in-check").append("<ul class='cart-header" + (i) + " simpleCart_shelfItem'><div onclick='click_on(" + (i) + "," + obj[i].ID + ")' class='close" + (i + 1) + "'></div><li class='ring-in'><a href='single.html?id=" + obj[i].ID + "'><img src='" + obj[i].IMAGE_URL + "' style='height:100px;width:150px' class='img-responsive' alt='ss'></a></li><li><span>" + obj[i].NAME + "</span></li><li><span class='item_price'>$" + obj[i].PRICE + "</span></li><li> <a href='#' class='add-cart cart-check item_add'>Add to cart</a></li><div class='clearfix'> </div></ul>");
            }
            $(".check_out-total").html("<hl class='total_value'>TOTAL:"+total+"</hl>");
        }
    });
}
function banner()
{
    t = Math.floor(Math.random() * (18 - 1)) + 1;
    if (t == 0)
    {
        t = 1;
    }
   
    //alert(country); 
    $.ajax({
        url: "js/server.php",
        data: ({id_value: t}),
        type: "POST",
        success: function (data) {
            console.log(data);
            var obj = jQuery.parseJSON(data);
            $(".banner").attr("src", obj.IMAGE_URL);
            $(".banner").attr("alt", obj.ID);
            $(".sub_image1").attr("src", obj.IMAGE_URL);
            $(".sub_image2").attr("src", obj.IMAGE_URL3);
            $(".blc-layer2").css("background", "url(" + obj.PICTURE_REAL + ")no-repeat");
            $(".main_banner").attr("href", "single.html?id=" + obj.ID);
            $(".item_add").attr("href", "checkout.html?id=" + obj.ID);
            $(".banner_price").html("$" + obj.PRICE);
            $(".banner_description").html(obj.DESCRIPTION);
            $(".banner_name").html(name = obj.NAME);
        }
    });
}
function check_out()
{
    
    var f_name = $(".f_name").val();
    var l_name = $(".l_name").val();
    var address1 = $(".address1").val();
    var address2 = $(".address2").val();
    var zip_code = $(".zip_code").val();
    var city = $(".city").val();
    var state = $(".state").val();
    $.ajax({
        url: "js/check_out.php",
        data: ({fname: f_name, lname: l_name, address1: address1, address2: address2, zip_code: zip_code, city: city, state: state}),
        type: "POST",
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            alert("purchase completed successfull");
            empty();
        }
    });
}
function past_orders() {

    $.ajax({
        url: "js/past_orders.php",
       
        type: "POST",
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            for (i = 0; i < obj.length; i++)
            {
                $(".in-check").append("<ul class='cart-header" + (i) + " simpleCart_shelfItem'><li class='ring-in'><a href='single.html?id=" + obj[i].P_ID + "'><img src='" + obj[i].IMAGE_URL + "' style='height:100px;width:150px' class='img-responsive' alt='ss'></a></li><li><span>" + obj[i].NAME + "</span></li><li><span class='item_price'>$" + obj[i].PRICE + "</span></li><li>" + obj[i].DATE + "</li><div class='clearfix'> </div></ul>");
            }
        }
    });
}
function empty()
{
    $.ajax({
        url: "js/empty_cart.php",
        type: "POST",
        success: function (data) {

            window.location = "checkout.html"
        }
    });
}
function temp() {
    $("#insertform").toggle();
}
function clean(){
                    for(i=0;i<9;i++)
                {
                temp = $(".image" + (i + 1));
                var hidden_ele = temp.closest(".home-product-main");
                $(hidden_ele).hide();
                }

  
}
function search()
{
    
    var query = window.location.search.substring(1);
    id = query.split("=").pop().trim();

    for (i = 1; i <= 20; i++)
    {
        ido_ele = "#" + i;
        $(ido_ele).prop('checked', false);
    }

    $.ajax({
        url: "js/search.php",
        data: ({search: id,cid:c_id,price:range,quantity:quantity_ele}),
        type: "POST",
        success: function (data) {
            
            var oj = jQuery.parseJSON(data);
            var obj = oj;
            console.log(obj)
            if (obj.length > 9)
                t = 9;
            else
                t = obj.length;
//alert(data.length);
clean();
            if(obj.length==0){
                            
               $("#"+c_id).prop('checked', 'checked') 
            }
            else
            {
                $(".product-block").css("visibility","visible")  
            for (i = 0; i < t; i++)
            {

                //alert("sdf");
                temp = $(".image" + (i + 1));
                temp1 = $(".image" + (i + 1) + "a");
                temp.attr("src", obj[i].IMAGE_URL);
                var price_ele = temp.closest(".home-product-main").find(".srch");
                var smart_ele = temp.closest(".home-product-main").find(".simagea");
                var hidden_ele = temp.closest(".home-product-main");
                $(hidden_ele).show();
                price_ele.html("<span> $" + obj[i].PRICE + "</span>");
                temp1.attr("href", "single.html?id=" + obj[i].PID);
                smart_ele.attr("href", "single.html?id=" + obj[i].PID);
                smart_ele.html(obj[i].NAME);
                ido_ele = "#" + obj[i].CID;
                $(ido_ele).prop('checked', 'checked');
               
 
            }
           
        }

        }
    });
}
function search_limits(id,cid,price,quantity)
{
    if(id!=20)
     price_id=id;
      if(cid==0&&price==0)
      {}
    else if(cid==0)
     range=price;
    else if(price==0)
     c_id=cid;
 else{
     c_id=cid;
        range=price;
 }
 quantity_ele=quantity;
 search();
  $("#"+price_id).prop('checked', 'checked');
 $("#"+id).prop('checked','checked');
 
}
function coupon()
{
 var coupon=$("#CheckOut-coupon").val();
 
$.ajax({
        url: "js/coupon.php",
        data: ({coupon:coupon}),
        type: "POST",
        success: function (data) {
            var obj = jQuery.parseJSON(data);
            console.log(data);
                  if(obj==false)
                      alert("Invalid Coupon");
                  else
                  {
                      dis=(total*obj.percent)/100;
                      total_dis=total-dis;
                     // alert(total);
                     
                      alert("Coupon Applied");
                       $(".check_out-total").html("<hl class='total_value'>TOTAL:"+total_dis+"</hl>");
                  }
}});
}