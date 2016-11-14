var SYSTEM = (function(){
    var obj = {};
	obj.boot = function(){
    		SYSTEM.Layout = new loadLayout();
    		loadFiltro();
    		SYSTEM.Toolbar  = loadToolbar();

	 }

    return obj;
})();

function loadLayout() {
  //atributos
  this.t;
  this.innerLayoutName = '3eLayout';
  this.innerLayout;
  this.outerLayout;
  var _panelPosition;
  var _amountPanelsLayout;
  var _outerLayoutName;
  var _textHeader;
  var _collapsedText;

  //Valor padr�o dos atributos,getters e setters

  Object.defineProperty(this, '_amountPanelsLayout', {
      value: this.innerLayoutName,
  });

  Object.defineProperty(this, '_outerLayoutName', {
      value: "1C",
      writable: true
  });

  Object.defineProperty(this, '_textHeader', {
        value: "Filtro<img src='../libs/layoutMask/imgs/filtro_icon.png' style='width: 21px; margin-top: 0px; margin-right: 7px; float: left' text='Filtro' alt='Filtro'/>",
      writable: true
  });

  Object.defineProperty(this, '_collapsedText', {
      value: "Filtro<img src='../libs/layoutMask/imgs/filtro_icon.png' style='width: 13px;  margin-top: 0px; margin-right: 7px; float: left' text='Filtro' alt='Filtro'/>",
      writable: true
  });

  Object.defineProperty(this, 'outerLayout', {
      value: new dhtmlXLayoutObject(document.body, this._outerLayoutName)
  });

  Object.defineProperty(this, 'innerLayout', {
      value: this.outerLayout.cells("a").attachLayout(this.innerLayoutName)
  });

  //m�todos
  this.executarConfiguracoesGlobais = function() {
    // setando o titulo
    this.innerLayout.cells("a").setText(this._textHeader);
    this.innerLayout.setCollapsedText("a", this._collapsedText);
  }

  this.executarModoCompatibilidade = function() {
    switch (this.innerLayoutName) {
    case '1C':
		this.tela: innerLayout.cells("a"),
		this.t1: function(titulo){
			innerLayout.cells("a").showHeader();
			innerLayout.cells("a").setText(titulo);
		}
    case '2uLayout':
        this.telaCima = this.innerLayout.cells("a");
        this.telaBaixo = this.innerLayout.cells("b");
      break;
      case '3eLayout':
      case 'giim3eLayout':
      this.telaCima = this.innerLayout.cells("a");
      this.telaMeio = this.innerLayout.cells("b");
      this.telaBaixo = this.innerLayout.cells("c");

      this.altura = function(alturaA,alturaB,alturaC){
        this.innerLayout.cells("a").setHeight(alturaA);
        this.innerLayout.cells("b").setHeight(alturaB);
        this.innerLayout.cells("c").setHeight(alturaC);
        },
  		this.t1 = function(titulo){
  			this.innerLayout.cells("a").setText(titulo);
  			this.innerLayout.setCollapsedText("a", titulo);
  		},
  		this.t2 = function(titulo){
  			this.innerLayout.cells("b").showHeader();
  			this.innerLayout.cells("b").setText(titulo);
  			this.innerLayout.setCollapsedText("b", titulo);
  		},
  		this.t3 = function(titulo){
  			this.innerLayout.cells("c").showHeader();
  			this.innerLayout.cells("c").setText(titulo);
  			this.innerLayout.setCollapsedText("c", titulo);
  		}
      break;
      case '3wLayout':
        this.telaEsq = this.innerLayout.cells("a");
        this.telaDir = this.innerLayout.cells("b");
        this.telaCima = this.innerLayout.cells("c");
      break;
      case 'gridLayout':
        this.telaCima = this.innerLayout.cells("a");
        this.telaBaixo = this.innerLayout.cells("b");
      break;

    }
  }

  this.t = function(panelPosition) {
     this._panelPosition = panelPosition;
     return this;
  }

  this.titulo = function(title, titleColapsed) {
    if (typeof titleColapsed === 'undefined') {
      titleColapsed = title;
    }

    this.innerLayout.items[this._panelPosition].setText(title);
    this.innerLayout.items[this._panelPosition].setCollapsedText(titleColapsed);
  }

  this.altura = function(altura){
     for (i = 0; i < _amountPanelsLayout; i++) {
        this.innerLayout.items[i].setHeight(altura[i]);
     }
  }

  // Deixando o obj compativel com a sintaxe antiga
  this.executarModoCompatibilidade();

  this.executarConfiguracoesGlobais();
}

function loadFiltro(){
  SYSTEM.Filtro = SYSTEM.Layout.innerLayout.cells("a").attachForm();
}

function loadToolbar(){
  var toolbar = SYSTEM.Layout.outerLayout.cells("a").attachToolbar();
  toolbar.setIconsPath("../libs/layoutMask/imgs/");
  toolbar.loadXML("../libs/layoutMask/dhxtoolbar.xml?etc=" + new Date().getTime());
  toolbar.attachEvent("onXLE", function(){
    toolbar.addSpacer("titulo");
    toolbar.forEachItem(function(itemId){
      toolbar.hideItem(itemId);
    });
  });
  return {
    core: toolbar,
    icones: function(iconsIds){
      setTimeout(function(){
        for(var i = 0; iconsIds.length > i ;i++){
          toolbar.showItem(iconsIds[i]);
        }
      }, 1000);
    },
    titulo: function (titulo){
            setTimeout(function(){
        toolbar.showItem('titulo');
        toolbar.setItemText('titulo', titulo);
      }, 1000);
    }
  }
}
