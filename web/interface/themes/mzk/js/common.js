$(document).ready(function(){
    // initialize autocomplete
    initAutocomplete();    

    // put focus on the "mainFocus" element
    $('.mainFocus').each(function(){ $(this).focus(); } );

    // support "jump menu" dropdown boxes
    $('select.jumpMenu').change(function(){ $(this).parent('form').submit(); });

    // attach click event to the "keep filters" checkbox
    $('#searchFormKeepFilters').change(function() { filterAll(this); });

    // attach click event to the search help links
    $('a.searchHelp').click(function(){
        window.open(path + '/Help/Home?topic=search', 'Help', 'width=625, height=510');
        return false;
    });

    // attach click event to the advanced search help links
    $('a.advsearchHelp').click(function(){
        window.open(path + '/Help/Home?topic=advsearch', 'Help', 'width=625, height=510');
        return false;
    });

    // assign click event to "email search" links
    $('a.mailSearch').click(function() {
        var id = this.id.substr('mailSearch'.length);
        var $dialog = getLightbox('Search', 'Email', id, null, this.title);
        return false;
    });

    // assign action to the "select all checkboxes" class
    $('input[type="checkbox"].selectAllCheckboxes').change(function(){
        $(this.form).find('input[type="checkbox"]').attr('checked', $(this).attr('checked'));
    });
    
    // attach mouseover event to grid view records
    $('.gridCellHover').mouseover(function() {
        $(this).addClass('gridMouseOver')
    });
    
    // attach mouseout event to grid view records
    $('.gridCellHover').mouseout(function() {
        $(this).removeClass('gridMouseOver')
    });  
    
    // assign click event to "viewCart" links
    //$('a.viewCart').click(function() {
    //    var $dialog = getLightbox('Cart', 'Home', null, null, this.title, '', '', '', null /*{viewCart:"1"}*/);
    //    return false;
    //});
    
    // Print
    var url = window.location.href;
    if(url.indexOf('?' + 'print' + '=') != -1  || url.indexOf('&' + 'print' + '=') != -1) {
        $("link[media='print']").attr("media", "all");
        window.print();
    }
    
    //ContextHelp
    contextHelp.init();
    contextHelp.contextHelpSys.load();
});


var contextHelp = {
        
    init: function() {
        $('body').append('<table cellspacing="0" cellpadding="0" id="contextHelp"><tbody><tr class="top"><td class="left"></td><td class="center"><div class="arrow up"></div></td><td class="right"></td></tr><tr class="middle"><td></td><td class="body"><div id="closeContextHelp"></div><div id="contextHelpContent"></div></td><td></td></tr><tr class="bottom"><td class="left"></td><td class="center"><div class="arrow down"></div></td><td class="right"></td></tr></tbody></table>');
    },
    
    hover: function(listenTo, widthOffset, heightOffset, direction, align, msgText) {     
        $(listenTo).mouseenter(function() {
            contextHelp.contextHelpSys.setPosition(listenTo, widthOffset, heightOffset, direction, align, '', false);
            contextHelp.contextHelpSys.updateContents(msgText);
        });
        $(listenTo).mouseleave(function() {
            contextHelp.contextHelpSys.hideMessage();
        });
    }, 
    
    flash: function(id, widthOffset, heightOffset, direction, align, msgText, duration) {
        this.contextHelpSys.setPosition(id, widthOffset, heightOffset, direction, align);
        this.contextHelpSys.updateContents(msgText);
        setTimeout(this.contextHelpSys.hideMessage, duration);
    },
    
    contextHelpSys: {
        CHTable:"#contextHelp",
        CHContent:"#contextHelpContent",
        arrowUp:"#contextHelp .arrow.up",
        arrowDown:"#contextHelp .arrow.down",
        closeButton:"#closeContextHelp",
        showCloseButton: true,
        curElement:null,
        curOffsetX:0,
        curOffsetY:0,
        curDirection:"auto",
        curAlign:"auto",
        curMaxWidth:null,
        isUp:false,
        load:function(){
            $(contextHelp.contextHelpSys.closeButton).click(contextHelp.contextHelpSys.hideMessage);
            $(window).resize(contextHelp.contextHelpSys.position)},
        setPosition:function(element, offsetX, offsetY, direction, align, maxWidth, showCloseButton){
            if(element==null){element=document}
            if(offsetX==null){offsetX=0}
            if(offsetY==null){offsetY=0}
            if(direction==null){direction="auto"}
            if(align==null){align="auto"}
            if(showCloseButton==null){showCloseButton=true}
            contextHelp.contextHelpSys.curElement=$(element);
            contextHelp.contextHelpSys.curOffsetX=offsetX;
            contextHelp.contextHelpSys.curOffsetY=offsetY;
            contextHelp.contextHelpSys.curDirection=direction;
            contextHelp.contextHelpSys.curAlign=align;
            contextHelp.contextHelpSys.curMaxWidth=maxWidth;
            contextHelp.contextHelpSys.showCloseButton=showCloseButton;},
        position:function(){
            if(!contextHelp.contextHelpSys.isUp||!contextHelp.contextHelpSys.curElement.length){return}
            var offset=contextHelp.contextHelpSys.curElement.offset();
            var left=parseInt(offset.left)+parseInt(contextHelp.contextHelpSys.curOffsetX);
            var top=parseInt(offset.top)+parseInt(contextHelp.contextHelpSys.curOffsetY);
            var direction=contextHelp.contextHelpSys.curDirection;
            var align=contextHelp.contextHelpSys.curAlign;
            if(contextHelp.contextHelpSys.curMaxWidth){
                $(contextHelp.contextHelpSys.CHTable).css("width",contextHelp.contextHelpSys.curMaxWidth)}
            else{
                $(contextHelp.contextHelpSys.CHTable).css("width","auto")}
            if(direction=="auto"){
                if(parseInt(top)-parseInt($(contextHelp.contextHelpSys.CHTable).height()<$(document).scrollTop())){
                    direction="down"}
                else{direction="up"}
            }
            if(direction=="up"){
                top = parseInt(top) - parseInt($(contextHelp.contextHelpSys.CHTable).height());
                $(contextHelp.contextHelpSys.arrowUp).css("display","none");
                $(contextHelp.contextHelpSys.arrowDown).css("display","block")}
            else{
                if(direction=="down"){
                    top = parseInt(top) + parseInt(contextHelp.contextHelpSys.curElement.height());
                    $(contextHelp.contextHelpSys.arrowUp).css("display","block");
                    $(contextHelp.contextHelpSys.arrowDown).css("display","none")}
                }
            if(align=="auto"){
                if(left+parseInt($(contextHelp.contextHelpSys.CHTable).width()>$(document).width())){
                    align="left"}
                else{align="right"}
            }
            if(align=="right"){
                left-=24;
                $(contextHelp.contextHelpSys.arrowUp).css("background-position","0 0");
                $(contextHelp.contextHelpSys.arrowDown).css("background-position","0 -6px")
            }
            else{
                if(align=="left"){
                    left-=parseInt($(contextHelp.contextHelpSys.CHTable).width());
                    left+=24;
                    $(contextHelp.contextHelpSys.arrowUp).css("background-position","100% 0");
                    $(contextHelp.contextHelpSys.arrowDown).css("background-position","100% -6px")}
            }
            if(contextHelp.contextHelpSys.showCloseButton) {
                $(contextHelp.contextHelpSys.closeButton).show();
            } else {
                $(contextHelp.contextHelpSys.closeButton).hide();
            }
            $(contextHelp.contextHelpSys.CHTable).css("left",left + "px");
            $(contextHelp.contextHelpSys.CHTable).css("top",top + "px");},
            
        updateContents:function(msg){
            contextHelp.contextHelpSys.isUp=true;
            $(contextHelp.contextHelpSys.CHContent).empty();
            $(contextHelp.contextHelpSys.CHContent).append(msg);
            contextHelp.contextHelpSys.position();
            $(contextHelp.contextHelpSys.CHTable).hide();
            $(contextHelp.contextHelpSys.CHTable).fadeIn()
            },
        hideMessage:function(){
            if(contextHelp.contextHelpSys.isUp){
                $(contextHelp.contextHelpSys.CHTable).fadeOut();
                contextHelp.contextHelpSys.isUp=false}
        }
    }
}

// return unique values from the given array
function uniqueValues(array) {
    var o = {}, i, l = array.length, r = [];
    for(i=0; i<l;i++) {
        o[array[i]] = array[i];
    }
    for(i in o) {
        r.push(o[i]);
    }
    return r;
}

