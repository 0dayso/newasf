//鼠标经过tips效果
$(function(){
   $('input,textarea').placeholder();
    $(".tips a").on({
        mouseover:function(tips){
           this.tipsTxt = this.title;
      //      this.tipsTxt = (this.tipsTxt.length>50?this.tipsTxt.toString().substring(0,50)+"...":this.tipsTxt);
           if (this.tipsTxt){
           //     alert( this.tipsTxt);
                this.tipsUrl = this.href;
                this.title = "";
                var tips = "<div id='tips'><p>"+this.tipsTxt+"</p><p>"+"</p></div>";
                $('body').append(tips);
                $('#tips').css({"opacity":"0.8"})
            }
        },
        mouseout:function(){
                this.title=this.tipsTxt;
                $('#tips').remove();
        },
        mousemove:function(tip){
            $('#tips').css({"top":(tip.pageY+22)+"px","left":(tip.pageX-10)+"px"});
        }
    });
});
