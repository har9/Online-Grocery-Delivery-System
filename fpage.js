
$(document).ready(function(){
    $("#login").css({"top": ((screen.height-400)/2).toString()+"px",
                    "left": ((screen.width-400)/2).toString()+"px",
                    "backgroundColor":"transparent",
                    "height":"130px", "width":"400px",
                    "position":"absolute"});
    
    var h = (((screen.height-400)/2)+120).toString()+"px";
    var wid = (((screen.width-400)/2)+320).toString() + "px";
    $("#regbutton").css({"height":"20px", "width":"55px", "borderRadius":"10px","border":"groove", "backgroundColor":"#0F7FF0", "top":h, "left":wid, "position":"absolute"});
    
    $("#regbutton").click(function(){
       window.open("createcustomer.html", "_blank"); 
    });
});