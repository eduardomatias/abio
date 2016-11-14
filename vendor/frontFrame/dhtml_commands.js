var telaSystem;

(function(){
    var System = {
        Main: {
            Layout : {},
            Tabbar : {}
        }
    }

    // Configs de Skin e path
    dhtmlx.image_path = "../assets/dhtmlx/terrace/imgs/";
    dhtmlx.skin = "dhx_terrace";

    //loadLayout(System);
    loadTabbar(System);
    openTabbar(System);
    alertsRefresh(System);


    $(".sidebar-toggle").on("click",function(){
        setTimeout(function(){
            System.Main.Tabbar._setSizes();
        }, 500);

    });

    $(window).on("resize",function(){
        setTimeout(function(){
            System.Main.Tabbar._setSizes();
        }, 500);
    });

    $("#pesquisar").on("keyup",function(e){

        if( $("#query").val() != "" ){
            findMenuItem($("#query").val());
        }else{
            resetMenu();
        }
    });

    $("#pesquisar").on("submit",function(e){
        e.preventDefault();
    });

    telaSystem = System;

}());

function resetMenu(){
    var itensMenu = $("#menu").find(".tagBusca");
    for(var i=0; itensMenu.length > i ; i++){
                    // fechando todos itens
        var parents = $(itensMenu[i]).parentsUntil( $("#menu") , 'ul'  );
            for(var j =0; parents.length > j ; j++){
                $(parents[i]).parent('li').removeClass("active");
                $(parents[i]).removeClass("active");
                $(parents[j]).removeClass("menu-open");
                $(parents[j]).css("display","none");
            }
        $(itensMenu[i]).css('display','block');
    }
}

function findMenuItem(string){
    var itensMenu = $("#menu").find(".tagBusca");
    for(var i=0; itensMenu.length > i ; i++){
                    // fechando todos itens
        var parents = $(itensMenu[i]).parentsUntil( $("#menu") , 'ul'  );
        for(var j =0; parents.length > j ; j++){
            $(parents[i]).parent('li').removeClass("active");
            $(parents[i]).removeClass("active");
            $(parents[j]).removeClass("menu-open");
            $(parents[j]).css("display","none");
        }
        $(itensMenu[i]).css('display','block');
    }
    // abrindo a árvore do item procurado
    for(var i=0; itensMenu.length > i ; i++){
        if(itensMenu[i].innerText.toLowerCase().indexOf(string.toLowerCase()) != -1  ){
            var parents = $(itensMenu[i]).parentsUntil( $("#menu") , 'ul'  );
            var main = $(itensMenu[i]).parentsUntil( $("#menu") , 'li'  );
            for(var j =0; parents.length > j ; j++){
                $(parents[i]).parent('li').addClass("active");
                $(parents[i]).addClass("active");
                $(parents[j]).addClass("menu-open");
                $(parents[j]).css("display","block");
            }
                        //console.log($(main[main.length-1]));
        }else{
            $(itensMenu[i]).css('display','none');
        }
    }
}

function loadLayout(System){
    System.Main.Layout = new dhtmlXLayoutObject("layoutArea", "1C");
}

function loadTabbar(System){
    System.Main.Tabbar = new dhtmlXTabBar("layoutArea");
    System.Main.Tabbar.setHrefMode("iframes");
    System.Main.Tabbar.enableAutoReSize(true);
    System.Main.Tabbar.enableTabCloseButton(true);
}

function alertsRefresh(System){
    setInterval(function(){
         $.ajax({
            url:'index.php?r=default/jsonalertas',
            success: function(resposta){
                if(resposta.logout === true){
                    window.location.href = "index.php?r=Seguranca/login/logout";
                }else if(resposta.refreshMenu === true) {
                   menuRefresh(System);
                }
                 //console.log(resposta);
            }
        });
    }, 5000); // tempo de verificação para refresh de menu e logout -> 1000 = 1 segundo
}

function menuRefresh(System){
    $.ajax({
        url:'index.php?r=default/refreshmenu',
        success: function(resposta){
            $("#menu").html('<li class="header"> </li>' + resposta);
            openTabbar(System);
        }
    });
}

function openTabbar(System){
    $(".sidebar-menu div").on("click",function(e){
        e.preventDefault();
        var url = $(this).attr("data-url");
        var name = $(this).text();
        var allTabs = System.Main.Tabbar.getAllTabs();
        var rowid = Date.now();
        if( url !== "#" ){
            /*
            // solicitado para comentar por Rafael em 15/06/2016
            // codigo para nao permitir abrir mais de 1 aba do programa ao mesmo tempo.
            for(var i = 0; allTabs.length >= i; i++){
                            if(url == allTabs[i]){
                                            System.Main.Tabbar.setTabActive(url);
                                            return;
                            }
            }
            */
            var tamanho_tab = name.length * 8 + 30;
            tamanho_tab = tamanho_tab+'px';
                System.Main.Tabbar.addTab(rowid,name,tamanho_tab);
                System.Main.Tabbar.setTabActive(rowid);

                System.Main.Tabbar.setContentHref(rowid,url);
        }
    });

    $(".enviaTab div").on("click",function(e){
        e.preventDefault();
        var url = $(this).attr("data-url");
        var name = $(this).text();
        var allTabs = System.Main.Tabbar.getAllTabs();
        var rowid = Date.now();
        if( url !== "#" ){
                        /*
                        // solicitado para comentar por Rafael em 15/06/2016
                        // codigo para nao permitir abrir mais de 1 aba do programa ao mesmo tempo.
                        for(var i = 0; allTabs.length >= i; i++){
                                        if(url == allTabs[i]){
                                                        System.Main.Tabbar.setTabActive(url);
                                                        return;
                                        }
                        }
                        */
            var tamanho_tab = name.length * 8 + 30;
            tamanho_tab = tamanho_tab+'px';
            System.Main.Tabbar.addTab(rowid,name,tamanho_tab);
            System.Main.Tabbar.setTabActive(rowid);

            System.Main.Tabbar.setContentHref(rowid,url);
        }
    });
}

function abrirTab(url,texto) {
    var url = url;
    var name = texto;
    var rowid = Date.now();
    var allTabs = telaSystem.Main.Tabbar.getAllTabs();
    if( url !== "#" ){
        /*
        // solicitado para comentar por Rafael em 15/06/2016
        // codigo para nao permitir abrir mais de 1 aba do programa ao mesmo tempo.
        for(var i = 0; allTabs.length >= i; i++){
                        if(url == allTabs[i]){
                                        telaSystem.Main.Tabbar.setTabActive(url);
                                        return;
                        }
        }
        */
        var tamanho_tab = name.length * 8 + 30;
        tamanho_tab = tamanho_tab+'px';
        telaSystem.Main.Tabbar.addTab(rowid,name,tamanho_tab);
        telaSystem.Main.Tabbar.setTabActive(rowid);
        telaSystem.Main.Tabbar.setContentHref(rowid,url);
    }
}
