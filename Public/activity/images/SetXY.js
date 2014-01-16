function  GetXY(txtid,searchid)
    {
        var obj=document.getElementById(searchid);
        var rela=document.getElementById(txtid);

        var x=rela.offsetLeft+1;
        var y=rela.offsetTop+rela.offsetHeight;
        var c=rela;
        while(c.offsetParent){
            c=c.offsetParent;
            x+=c.offsetLeft;
            y+=c.offsetTop
        }
        obj.style.left=x+"px";
        obj.style.top=y+"px";
    }