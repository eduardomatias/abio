if (!window.des) var des = {};

des.get_children= function(id){
	var pull = [];
	var tree = $$('leftTree');
	//pull.push(tree.item(id));
	if(tree.data.branch[id])
		des.bypass_branches(tree.data.branch[id], pull, tree);
	return pull;
};
des.bypass_branches = function(branch, pull, tree){
	if(branch)
		for(var i=0; i < branch.length; i++){
			pull.push(tree.data.pull[branch[i]]);
			if(tree.data.branch[branch[i]])
				des.bypass_branches(tree.data.branch[branch[i]], pull, tree);
		}
};

des.imagedMenuItem = function(image, text){
	return "<div class='dhx_menu_img' style='background-image:url(\""+image+"\")'></div><span class='dhx_menu_im_text'> "+text+"</span>";
};




des.save_snippets = function(){

	var new_pull = [];
	new_pull = des.get_children('snippets');	
	
	var data = JSON.stringify(new_pull);
	localStorage.setItem('shippets', data);
};
des.load_snippets = function(){
	var snippets = localStorage.getItem('shippets');
    if(snippets){
		var tree = $$('leftTree');
		var data = dhx.DataDriver.json.toObject(snippets);
		data = data.sort(function(a,b){return (a.$level-b.$level);});
		for(var i=0; i < data.length; i++){
			data[i].$selected = 0;
			tree.add(data[i], -1, data[i].$parent);
		}
		tree.open('snippets');
	}
};
des.start_download = function(){
	var form = document.createElement('form');
	form.method = 'post';
	form.target = '_blank';
	form.action = './codebase/php/des.php?task=download';
	var input = document.createElement('input');
	input.type = 'hidden';
	input.name = 'code';
	input.value = des.get_code();
	form.appendChild(input);
	if(des.writer.skin){
		var input = document.createElement('input');
		input.type = 'hidden';
		input.name = 'skin';
		//var skins = [];
		//for(var i in des.writer.skins)
		//	skins.push(i);
		input.value = des.writer.skin;
		form.appendChild(input);
	}


	
	document.body.appendChild(form);
	form.submit();
	document.body.removeChild(form);
};

des.new_page = function(){
	des.save_snippets();
	$$('leftTree').clearAll();
	$$('details').clearAll();
	$$('events').clearAll();
	
	var data = des.deep_copy(des.config.initial);
	des.names.reset();
	des.leftTree.parse(data,'json');
	des.one_per_page = {};
	
	des.load_snippets();
	des.pro.clearProp();
    des._was_changed = true;
};

des.save = function(){
	var params = '';
	var data = [];
	
	//$$('leftTree').data.serialize returns array of elements on deepest level of tree only
	//so they gets manually
	for(var dat in $$('leftTree').data.pull){
		if((dat != 'snippets')&&!($$('leftTree').data.branch.snippets && $$('leftTree').data.branch.snippets[dat])){
			data.push($$('leftTree').data.pull[dat]);
		}
	}
	data = data.sort(function(a,b){return (a.$level-b.$level);});


	data = encodeURIComponent(JSON.stringify(data).replace(/'/g, "\\'"));
	var code = encodeURIComponent(des.get_code());
	
	params += 'data=' + data + '&code=' + code;

	dhx.ajax().post("./codebase/php/des.php?task=save", params, function(response) {
		dhx.notice({
			message:"Saved..."			
		});
	});

};

des.load = function(){
	
	dhx.ajax().post("./codebase/php/des.php?task=load", '', function(response) {
		if (response){
			var stored_data = '';
			dhx.exec(response);
			
			if(stored_data){
				
				$$('details').clearAll();
				$$('events').clearAll();
				des.one_per_page = {};
				var data = stored_data;
				$$('leftTree').data.clearAll();
				data = dhx.DataDriver.json.toObject(data);
				
				var root = [];
				for(var item in data){
					data[item].$selected = false;
					if(data[item].$level == 1){
						root.push(data[item]);
						delete data[item];
					}

				}
				$$('leftTree').parse(root);//place root elements('main ui' and 'popups')
				for(var i = 0; i < data.length; i++){
					if(data[i]){
						if(des.nesting._one_per_page[data[i].element])
							des.one_per_page[data[i].element] = true;
						$$('leftTree').add(data[i], -1, data[i].$parent);
					}
				}
				dhx.notice("Data loaded");
				des._was_changed = true;
			}else{
				dhx.notice({message:"Couldn't load data..."});
			}
		}else{
			dhx.notice({message:"Couldn't load data..."});
		}
	});
};

des.details_tabbar = function(id, event){
	$$($$('properties_view').value).show();
	//$$(dhx.html.locate(event, "button_id")).show();
};

des.helper_tabbar = function(id, event){
	var action = dhx.html.locate(event, "key");
	switch (action){
		case "up" :
			des.selection_up();
		break;
		case "down" :
			des.selection_down();
		break;
		case "left" :
			des.selection_left();
		break;
		case "right" :
			des.selection_right();
		break;
		default:
			break;
	}
};
//define click handlers before ui activation
des.selection_down = function(){
	var selection = $$('leftTree').getSelected();
	if (!selection) return false;
	var id = $$('leftTree').nextSibling(selection);
	if (id)
		$$('leftTree').select(id);
};
des.selection_up = function(){
	var selection = $$('leftTree').getSelected();
	if (!selection) return false;
	var id = $$('leftTree').prevSibling(selection);
	if (id)
		$$('leftTree').select(id);
};
des.selection_left = function(){
	var selection = $$('leftTree').getSelected();
	if (!selection || selection=='top') return false;
	var id = $$('leftTree').parent(selection);
	if (id && id != 'windows')
		$$('leftTree').select(id);
	else
		return false;
};
des.selection_right = function(){
	var selection = $$('leftTree').getSelected();
	if (!selection) return false;
	var id = $$('leftTree').firstChild(selection);
	if (id)
		$$('leftTree').select(id);
};	
des.remove_selected = function(){
	var selection = $$('leftTree').getSelected();
	var node = $$('leftTree').item(selection);
	if(node){
		if(selection == 'top')
			dhx.alert({title: "Designer", message: "You can't delete root element"});
		else if(node.element == 'cell')
			dhx.alert({title: "Designer", message: "You can't delete layout's cell"});
		else
			dhx.confirm({
				title: "Designer",
				message: "Are you sure you want to remove "+(node._backup||node._text)+"?",
				callback: function(result) {
					if (result){
						if(des.nesting._can_enable[node.element]){
							$$('leftTree').item(node.$parent)[node.element] = 0;
						}else if(des.nesting._can_set[node.element]){
							$$('leftTree').item(node.$parent).body = 'none';
						}

						
						$$('leftTree').remove(selection);
						if(node.element == 'context_menu')
							des.update_context_menus();
						$$('details').clearAll();
						des._was_changed = true;
						result = des.pro.checkSaved();
						des.pro.show(result);
						des.backup.set_config();			
						des.update_one_per_page();
					}
				}
			});
	}
};
des.change_content_confirm = function(id,itemId, callback){
	if(typeof itemId == 'object')
		var item = itemId;
	else
		var item = $$('leftTree').item(itemId);
    dhx.confirm({
		title: "Designer",
		message: "Are you sure you want to "+ (id=='body' ? ("switch content of " +
			(item._backup||item._text)) : ("remove "+id)) +"?",
		//	($$('leftTree').item(itemId)._backup)||$$('leftTree').item(itemId)._text) : ("remove "+id)) +"?",
		callback: function(result) {
			if(typeof callback == 'function')
				callback(result);
			des._was_changed = true;
		}
	});
};
des.hide_datasources = function(){
	$$('datasources_win').hide();
	des.fill_datasources();
};
des.show_datasources = function(e){
	alert('aaa');
};
des.update_context_menus= function(){
	var tree = $$('leftTree');
	var element = {};
	var context_menus = {};
	context_menus[''] =  'None';
	var index = 1;
	var indexes = [];
	
	tree.data.eachChild('windows', function(id){				
		element = tree.item(id);
		if(element.element == 'context_menu'){
			if(element.index){
				context_menus[id] = element.name;
				indexes.push(element.index);
				indexes = indexes.sort(function(a,b){return (a-b);});
			}else{
				element.index = index;
				var last = 1 + (indexes.length  ? indexes[indexes.length-1] : 0);

				context_menus[id] = element.name;
			}
			index++;						
		}
	});
	des.context_menus_list = context_menus;
};

des.fill_datasources = function(){
	datasourceHelper.updateLocalStorage();

	var data = {"dhx_not_used":"Not set"};
	var urls = {};
        
	if($$('leftTree').getSelected()){
	   $$('datasources_list').data.each(function(obj){
		   urls[obj.name] = obj.url;
		   if(obj.type == $$('leftTree').item($$('leftTree').getSelected()).element)
			   data[obj.name] = obj.name;
		});
	}
	else{
		$$('datasources_list').data.each(function(obj){
		   urls[obj.name] = obj.url;
		});
	}
	des.datasources_select_list = data;
	des.datasources_ulr_list = urls;
};

des.block_drag = function(context){    
    
	context.html = "<div style='font-size: 10pt;font-family: Tahoma;cursor: default;color: #000000;background-color:white;padding:0 5px;'>"+(this.item(context.source[0])._backup||this.item(context.source[0])._text)+"</div>";
	
	var stype = this.item(context.source[0]).element;
	return !!((des.nesting._can_add[stype] || des.nesting._can_set[stype] || des.nesting._can_enable[stype]) && (this.item(context.source[0]).id != 'top') && (this.item(context.source[0]).element != 'collection'));
};

des.block_invalid_drop = function(context){
	var source = this.item(context.source[0]);
	var target = this.item(context.target);
	//cant drop element on empty space
	if(!context.target)// || context.target == 'top')
		return false;

	//deny drop element on its child node
	var temp = target;
	while(temp.$parent && temp.$parent != 'top'){
		if(temp.$parent == context.source[0])
			return false;
		temp = this.item(temp.$parent);
	}
	//deny drop 'enabled' or 'setted' element on its parent node
	if(des.nesting._can_enable[source.element] || des.nesting._can_set[source.element]){
		if(target.id == source.$parent)
			return false;
	}
	if(!des.nesting[target.element][source.element]){
		if(target.$parent && des.nesting[this.item(target.$parent).element][source.element] && des.nesting._can_add[source.element])
			context.mode = "sibling";
		else
			context.mode = "child";
	}else
		context.mode = "child";
	//cant drop element on itself
	return !(context.source[0] == context.target);

};


des.containing_one_per_page_elements = function(id){
	var tree = $$('leftTree');
	
	var wanted = [];
	
	var new_pull = des.get_children(id);
	new_pull.push(tree.item(id));
	
	
	for(var j in new_pull){
		if(des.nesting._one_per_page[new_pull[j].element])
			wanted.push(new_pull[j].element);
	}
	return wanted;
};

des.confirm_not_allowed_elements = function(message){
	dhx.notice({
				message:message,
				height:'auto'
			});
};

des.check_is_drop_allowed = function(context){
	if(context.mode == "sibling"){
		des.backup.backup();
		return true;
	}
	var source = this.item(context.source[0]);
	var target = this.item(context.target);

	var els = [];

	var targ_els = [];
	//collects elements which can be placed only one per page(only scheduler for now)
	if(des.is_snippet(source.id)){
		//so it wount be possible to create several of such elements
		//by copying them from snippets
		els = des.containing_one_per_page_elements(source.id);
	}
	targ_els = des.containing_one_per_page_elements(target.id);

	if(des.nesting._can_enable[source.element] && (target.$parent == source.$parent))
		return false;
	if(!des.nesting[target.element][source.element]){
		
		context.tindex = this.branchIndex(target.$parent, context.target);
		context.target = target.$parent;
		target = this.item(target.$parent);
	}

	if(target != undefined){

		if(des.nesting[target.element][source.element]){

			if(des.nesting._can_enable[source.element]||des.nesting._can_set[source.element]){

				var check_type = des.nesting._can_enable[source.element] ?  source.element : des.nesting._can_set[target.element] ? target.element : this.item(context.target).body;
				var exist_id = des.exists(this, context.target, check_type);
				if (exist_id && !des.is_snippet(context.target)){
					for(var i = 0; i < els.length; i++){
						if(des.one_per_page[els[i]]){
							//des.confirm_not_allowed_elements('Only one element of type "'+els[i]._text+'" is allowed on page');
							return false;
						}
					}
					dhx.confirm({
						title: "Designer",
						message: "Are you sure? Dropping "+source.element+" here will delete existing "+($$('leftTree').item(exist_id)._backup||$$('leftTree').item(exist_id)._text)+" data.",
						callback: function(result) {
							if(result){

								des.backup.backup();
								des.update_dnd_source(context.source[0]);
								context.target = target.id;
								if(des.is_snippet(context.source[0])&&!(des.is_snippet(context.target))){

									var targ = des.deep_copy(source);
									
									var parent = target.id;
									$$('leftTree').remove(exist_id);
									des.copy_tree_node(targ, parent, true, des.confirm_not_allowed_elements);
									$$('leftTree').open(parent);
								}else{
									$$('leftTree').remove(exist_id);
									$$('leftTree').move(context.source[0], context.target);
								}
								des.correct_after_dnd(context);
							}
						}
					});
					return false;
				}else if(des.is_snippet(context.source[0])&&!(des.is_snippet(context.target))){

					for(var i = 0; i < els.length; i++){
						if(des.one_per_page[els[i]])
							return false;
					}
					des.backup.backup();
					var targ = des.deep_copy(source);
				//	targ.name = des.names.get_next(targ.element);
				//	targ._text = des.protos[targ.element]._text + ' : <span class="dhx_element_name">'  + targ.name + '</span>'
					des.copy_tree_node(targ, target.id, true, des.confirm_not_allowed_elements);
					$$('leftTree').open(target.id);
					des.correct_after_dnd(context);

					return false;
				}else{
					des.backup.backup();
					des.update_dnd_source(context.source[0]);
					des.correct_after_dnd(context);
					return true;
			}

			}else if(des.nesting._can_add[source.element]){
				if(des.nesting.container[ $$('leftTree').item(source.$parent).element]){
					des.backup.backup();
					var targ = des.deep_copy(source);
					des.copy_tree_node(targ, target.id);
					$$('leftTree').open(target.id);
					des.correct_after_dnd(context);
					$$('leftTree').select('top');
					return false;
				}else{
					des.backup.backup();
					des.correct_after_dnd(context);
					return true;
				}
			}
		}else{
			return false;
		}
	}
	return false;
};
des.copy_tree_node = function(sour, targ, check, invalid_element_callback){
	var source = des.deep_copy(sour);
	
	source.$parent = targ;
	
	var tree = $$('leftTree');
	
	var tmp_pull = [];
		
	tmp_pull = des.get_children(source.id);
	var new_pull = [source];
	for(var i in tmp_pull){
		if(!check || !des.one_per_page[tmp_pull[i].element]){
			var el = des.deep_copy(tmp_pull[i]);
			if(el.$selected)
				delete el.$selected;
			new_pull.push(el);
			//if(check && des.nesting._one_per_page[tmp_pull[i].element])
			//	des.one_per_page[tmp_pull[i].element] = true;
		}else{
			if(typeof invalid_element_callback == 'function'){
				invalid_element_callback('Only one element of type "'+tmp_pull[i]._backup||tmp_pull[i]._text+'" is allowed on page');
			}
		}
	}
	
	var old_id = source.id;	
	delete source.id;
	if(source.$selected)
				delete source.$selected;
	var new_id = source.id;
	var changedNames = {};
	var conflictNames = false;
	for(var i = 0; i < new_pull.length; i++){
		for(var j = 0; j < new_pull.length; j ++){
			if(new_pull[j].$parent == old_id)
				new_pull[j].$parent = new_id;
		}
		old_id = new_pull[i].id;
		delete new_pull[i].id;
		if(!des.isNameUnique(new_pull[i].name)){
			conflictNames = true;
			var new_name = des.names.get_next(new_pull[i].element);

			changedNames[new_pull[i].name] = new_name;
			new_pull[i].name = new_name;
		}
		new_pull[i]._text =des.element_text(new_pull[i].element, new_pull[i].name);
		tree.add(new_pull[i], -1, new_pull[i].$parent);
		
		new_id = new_pull[i].id;
	}

	if(conflictNames){
		des.show_name_duplication_label(changedNames);
	}

};


des.show_name_duplication_label = function(matches){
	var pairs = '';
	for(var p in matches){
		pairs += ("<br/><b>" + p + "</b> --> <b>" + matches[p]+"</b>");
	}
	var mess = "Following snippet elements were renamed to prevent names conflict:" + pairs;
	dhx.notice({
		message:mess,
		delay:5000,
		height:'auto'
	});
};


des.update_dnd_source = function(sourc){
    var tree = $$('leftTree');

    var source = tree.item(sourc);
	if(!des.nesting.container[ tree.item(source.$parent).element ]){
		if(des.nesting._can_enable[source.element]){
			tree.item(source.$parent)[source.element] = 0;
			des.refresh_details(source.$parent, tree.item(source.$parent));
		}else if(des.nesting._can_set[source.element]){
			tree.item(source.$parent).body = 'none';
			des.refresh_details(source.$parent, tree.item(source.$parent));
		}
	}
};

des.correct_after_dnd = function(context){
	var tree = $$('leftTree');
	var target = tree.item(context.target);
	var source = tree.item(context.source);

	if(des.nesting._can_enable[source.element]){
		target[source.element] = 1;
	}else
		if(des.nesting._can_set[source.element]){
			target.body = source.element;
		}
	tree.select(context.target);
	
	des.update_one_per_page();
	des.refresh_details(target.id, target);
	des._was_changed = true;

	
};
des.update_one_per_page = function(){
	des.one_per_page = {};
	des._update_one_per_page('top');
	des._update_one_per_page('windows');
};
des._update_one_per_page = function(rid){

	if(des.nesting._one_per_page[$$('leftTree').item(rid).element]){
		des.one_per_page[$$('leftTree').item(rid).element] = true;
	}
	$$('leftTree').data.eachChild(rid, function(id){
		des._update_one_per_page(id);
	});
};
des.refresh_details = function(id, data){
	if ($$('leftTree').getSelected() == id)
	$$('details').setValues(data);
};











dhx.ready(function(){
    dhx.ui({
        type:"wide", cols:[{
            rows:[{
                view:"toolbar", css:"dhx_bluebar",  id:"topToolbar", elements:[
					{id: "settings", align: "right", label:"",view:"menu_button", src: 'codebase/images/icons/settings_icon.png',width:50, margin:10,
						submenu: [
						
							{id:"new", text:"New",click:des.new_page}
							,{id:"save", text:"Save", click:des.share.save}
//							{id:"download", text:"Download", click:des.start_download}//,
							//{id:"load", text:"Load", click:des.load}
						]
					},
					{view:"two_state_button", id:"is_preview", width:50, src:"codebase/images/icons/preview_icon.png", align:"right",  click:(function(){des._was_changed = true;})},
					{view:"hover_button", id:"undo", width:50, src:"codebase/images/icons/undo_icon.png", align:"right", click: function() {des.backup.undo();}},
					{view:"toggle_blue", id:"preview_mode", align:"center", widths:[100, 100], options:[{value:"Code",label:"Code"}, {value:"Design",label:"Design"}], value:"Design", click:(function(){des.preview();})},
					{view:"label", id: "pro_label", label: "", width: 100, css: 'pro'},
					{view:"hover_button",width:50, src:"codebase/images/icons/refresh_icon.png", align:"center", marginRight:'10px',  click:des.preview}
              ]
            },{
              view:"iframe", id:"preview"
            }]
        },{
            width:300, rows:[
{
          	view:"toolbar", css:"dhx_bluebar", id:"help_bar",  elements:[
				/*	{id:'hover', type:"hover_segmented" , segment:[
						{key:"up", src:"codebase/images/icons/up_icon.png"},
						{key:"down", src:"codebase/images/icons/down_icon.png"},
						{key:"left", src:"codebase/images/icons/left_icon.png"},
						{key:"right", src:"codebase/images/icons/right_icon.png"}
            		], click:des.helper_tabbar},
*/
					{view:"hover_button",  id:"sibling", align:'right', width:60,  src:"codebase/images/icons/sibling_icon.png"/*,click:dhx.show_context_menu,	popup:"main_context_menu"*/},
					{view:"hover_button", align:"center", id:"child", width:60,marginLeft:'5px', src:"codebase/images/icons/children_icon.png"/*,click:dhx.show_context_menu, popup:"main_context_menu"*/},
					{view:"dummy"},
					{view:"hover_button", id:"delete", align:"right", width:50, light:true, marginRight:'10px',src:"codebase/images/icons/delete_button.png",	click:des.remove_selected}

            	]
          }
				,
				{
                view:"tree",
	            id:"leftTree",
				select:true,
				drag:true,
				type:{
					templateCommon:function(obj,type){
						var icon2;
						if(obj.item)
							icon2 = "tree_folder_"+(obj.open?"open":"close");
						else
							icon2 = obj.$level==1?"tree_folder_close":"tree_leaf";

						return "<div>"+type.icon(obj,type)+"<div class='"+icon2+"'></div>"+obj._text+"</div>";
					}
				}
            }, {
				id:"properties_toggle",
				view:"toolbar",
				css:"dhx_bluebar",
					elements:[
						{view:"toggle_blue", id:"properties_view", align:"center", widths:[120, 120], options:[
								{label:"Properties", value:"details"},
								{value:"events",label:"Events"}],
							value:"details", click:des.details_tabbar}
            	]
            }
			,{
            	view:"multiview", 
				id:"details_tabbar"
		    	, cells:[
            		{
            			view:"property_tooltip", id:"details",
    					nameWidth:120, scroll:'auto', elements:[]
            		},
            		{ 
            			view:"property_tooltip", id:"events",
    					nameWidth:120, scroll:'auto', elements:[]
            		}
            	]
            },{
				id:"rightToolbar"
				, view:"toolbar",  css:"dhx_bluebar",  elements:[
					{view:"label", label: "DHTMLX Designer<br><span class='dhx_rightbar_div'>Version 1.30</span>", align:'right'}
					]
		}
			]
        }]
    });

	dhx.event($$('leftTree').getNode(), 'selectstart', function(e){//forbid selection in ie
		if(e.preventDefault)e.preventDefault();
		else e.returnValue=false;
	});

	$$('sibling').getNode().setAttribute("title", des.tooltips["toolbar_buttons"]["sibling"]);
	$$('child').getNode().setAttribute("title", des.tooltips["toolbar_buttons"]["child"]);
	$$("delete").getNode().setAttribute("title", des.tooltips["toolbar_buttons"]["delete"]);
	$$('is_preview').change_state($$('is_preview')._viewobj.firstChild.firstChild, $$('is_preview'));

	dhx.ui({
	    view:"context", id:"align_distance",
	    body:{
	        view:"list",
	        scroll:false,
			id:"align_distance_options",
			data:[
				{id:'LR', text:des.imagedMenuItem("codebase/images/form_designer/distance_hor.png",' Horizontal')},
				{id:'TB', text:des.imagedMenuItem("codebase/images/form_designer/distance_vert.png",' Vertical')}
		
			],
	        type:{
				template:"#text#",
	            width:100,
	            height:22,
	            padding:5,
	            css:"context_menu"
	        },
	        yCount:"auto"
        }
	}).hide();

	dhx.ui({
	    view:"context", id:"align_position",
	    body:{
	        view:"list",
	        scroll:false,
			id:"align_position_options",
			data:[
				{id:'L', text: des.imagedMenuItem("codebase/images/form_designer/alignment_left.png",' Left')},
				{id:'R', text: des.imagedMenuItem("codebase/images/form_designer/alignment_right.png",' Right')},
				{id:'T', text: des.imagedMenuItem("codebase/images/form_designer/alignment_top.png",' Top')},
				{id:'B', text: des.imagedMenuItem("codebase/images/form_designer/alignment_bottom.png",' Bottom')}
			],
	        type:{
				template:"#text#",
	            width:100,
	            height:22,
	            padding:5,
	            css:"context_menu"
	        },
	        yCount:"auto"
        }
	}).hide();

    dhx.ui({
	    view:"context", id:"main_context_menu",
	    body:{
	        view:"list",
	        scroll:false,
			id:"options",
	        type:{
	            width:100,
	            height:22,
	            padding:5,
	            css:"context_menu"
	        },
	        yCount:"auto"
        },
	    master:$$('leftTree')
	}).hide();
	
	
	dhx.ui({
	    view:"window",
	    id:"datasources_win",
	    modal:true,
		head:{
	    	view:"toolbar", css:"dhx_bluebar", elements:[
	    		{view:"label", label:"Data Sources",css:"datasources_label"},
				{view:"imagebutton", src:"codebase/images/icons/new_icon.png", width:60, align:"left", click: datasourceHelper.add},
				{view:"imagebutton", src:"codebase/images/icons/bin_icon.png", width:60, align:"left", click: datasourceHelper.remove},
				{view:"imagebutton", src:"codebase/images/icons/close_icon.png",width:60,  align:"right", click:des.hide_datasources}
				
				
	    	]
	    },
	    body:{
	    	cols:[{
	    			view:"list",
		        	id:"datasources_list",
		        	type:{
		        		template:"#name#",
		            	width:"auto"
			        },
			        select:true,
			        width:200
		        },{
					view:"property",
					id:"datasources_details",
					name_width:100,
					data:[
						{id:"name", name:"Name", type:"text"},
						{id:"url", name:"URL", 	 type:"text"},
						{id:"type", name:"Type", type:"select",  options: des.datasource_types, value:''}
					]
	            }
			]
      },
	    width:600, height:400, position:"center"
	}).hide();
       
	dhx.ui({
	    view:"window", id:"form_designer",
	    modal:true,		
	    width:window.document.body.offsetWidth - 100,
		height:window.document.body.offsetHeight - 50,
		position:"center",
	    head:{
	    	view:"toolbar", css:"dhx_bluebar", elements:[
				{view:"hover_button", src:"codebase/images/form_designer/save_icon.png", align:"right", rounder:true, width:45, small:true, click: dhx.designer.save},
				{view:"dummy", width:32},
				{view:"hover_button", src:"codebase/images/form_designer/delete_icon.png", id:"form_delete",rounder:true,  align:"center",  width:120, small:true, label:'Delete', disabled:true, click:dhx.designer.deleteElement},
				{view:"hover_button", src:"codebase/images/form_designer/alignment_icon.png", id:"form_align",rounder:true,  align:"center",  width:130, small:true, label:'Alignment',  popup:"align_position", disabled:true},
				{view:"hover_button", src:"codebase/images/form_designer/distance_icon.png", id:"form_distance",rounder:true,    align:'center', width:120, small:true,label:"Distance",	popup:"align_distance", disabled:true},
				{view:"dummy"},
				{view:"hover_button", id:"form_get_code",rounder:true,    align:'center', width:180, small:true,label:"View Code",	 click: dhx.designer.getCode},// if modified, update also
				{view:"dummy", width:155},                                                                                                                        //   designer.js->dhx.designer.getCode
				{view:"hover_button",   align:'right',rounder:true,small:true,  width:80, marginRight:'5px', label:"Close",	 click: dhx.designer.closeEditor}     //   code view uses same buttons
	    	]
	    },
	    body:{
	    	id:"designer",
			view:"abstractDesigner"
        }
	}).hide();
	window.show_form_designer = function(){
		dhx.designer.changes.reset();
		$$("form_designer").config.width = window.document.body.offsetWidth - 100;
		$$("form_designer").config.height = window.document.body.offsetHeight - 50;
		$$("form_designer").show();
		$$("form_designer").resize();

		$$('designer').$$('area')._viewobj.style.width = $$('designer').$$('area')._viewobj.parentNode.style.width.replace('px','') - 10 + 'px';
		$$('designer').$$('area')._viewobj.style.height = $$('designer').$$('area')._viewobj.parentNode.style.height.replace('px','') - 10 + 'px';
	};

	$$('form_designer').getNode().ondblclick=function(e){ 
		if(document.selection && document.selection.empty){
			document.selection.empty() ;
		} else if(window.getSelection) {
			var sel=window.getSelection();
			if(sel && sel.removeAllRanges)
				sel.removeAllRanges() ;
		}
	};
	$$('datasources_list').attachEvent("onBeforeSelect", function(){
		$$('datasources_details').stopEdit();
	});
	$$('datasources_list').attachEvent("onAfterSelect", function(id){           
            $$('datasources_details').setValues(this.item(id));
	});
        $$('datasources_list').attachEvent("onClose", function(id){
		des.fill_datasources();
            $$('details').setValues($$('leftTree').item($$('main_context_menu').config.treeParent));
	});
	datasourceHelper.fillDatasourceDialog();
    des.update_context_menus();
	//$$('datasources_list').attachEvent("onXLE", des.fill_datasources);
	$$('datasources_details').attachEvent("onAfterEditStop", function(){
		var list = $$('datasources_list');
		var id = list.getSelected();
		//actually we have a bug here, set must not clear selection
		//don't have time to fix it , so will use workaround for now
		if(!id)
			return false;
		dhx.extend(list.item(id), this.getValues(), true);
		list.refresh(id);
	});

	$$('leftTree').attachEvent("onStoreUpdated", function() {des.backup.set_config();});
	$$('leftTree').attachEvent("onBeforeDrag", des.block_drag);
	$$('leftTree').attachEvent("onBeforeDragIn", des.block_invalid_drop);
	$$('leftTree').attachEvent("onBeforeDrop", des.check_is_drop_allowed);
	$$('leftTree').attachEvent("onAfterDrop", des.correct_after_dnd);
	$$('leftTree').attachEvent('onBeforeSelect', function(id){
		//freezed elements are not selectable
		if (this.item(id).freeze  === true)
			return false;
		return true;
	});
	$$('details').attachEvent("onAfterEditStop", function(id){
		var selected = $$('leftTree').item($$('leftTree').getSelected());
		if(id == 'body' && selected.body && selected.body != 'none'){
			des.change_content_confirm(id,selected, function(result){
				if(result){
					var value = $$('details').item(id).value;
					des.property($$('leftTree'), $$('details')._linked_id, id, value);
					des.check_body_elements();
				}else{
					var tree = $$('leftTree');
					var treeid = $$('details')._linked_id;
					des.refresh_details(treeid,tree.item(treeid));
					des.check_body_elements();
				}
			});
		}else {
			var item = this.item(id);
			if(this.types[item.type].stopEdit)
				this.types[item.type].stopEdit(this.item(id));
			var value = this.item(id).value;		
			des.check_details_batch();		
			des.property($$('leftTree'), this._linked_id, id, value);
		}
		des.backup.set_config();
		des.check_body_elements();
		$$('leftTree').refresh();
	});

	des.check_body_elements = function(){
		if(!$$('details').item("body"))
			return true;
		var item = $$('leftTree').getSelected();

		if(!des.containing_one_per_page_elements("gui").length && !des.containing_one_per_page_elements("windows").length || des.is_snippet(item) || $$('details').item("body").value == "scheduler")
			$$('details').item("body").options["scheduler"] = "Scheduler";
		else{
			if($$('details').item("body").options["scheduler"])
				delete $$('details').item("body").options["scheduler"];
		}
		
	};

	des.check_details_batch = function(){
		$$('details').data.filter(function(item){
				if(item.batch){
					var dep = item.batch;
					for(var i in dep){
						if(!$$('details').item(i)){
							return false;
						}
						if(!dep[i][$$('details').item(i).value])
							return false;
					}
					return true;
				}else
					return true;

		});
	};

	des.create_confirm = function(config){
		if(!(config.id && config.save && config.dont_save)){
			throw "Invalid config";
		}
		config.text = config.text || "Do you want to save changes?";
		config.cancel = config.cancel || function(){$$(config.id).hide();}

		return dhx.ui({
			view:"window",
			id:config.id,
			modal:true,
			css:"confirm_window",
			head:{
				view:"toolbar", css:"dhx_bluebar", elements:[
					{view:"label", label:"<span style='font-size:22px;line-height:40px;'>"+config.text+"</span>"}
				]
			},
			body:{
				css:" confirm_menu",
				cols:[
					{view:"borderless_view",	rounder:true,  align:"center",  width:120, small:true, label:"Save", click:config.save},
					{view:"borderless_view",	rounder:true,  align:"center",  width:130, small:true, label:"Don't Save", click:config.dont_save},
					{view:"borderless_view",	rounder:true,  align:'center',  width:130, small:true,label:"Cancel", click:config.cancel}
				],
				height:55
			}
			,position:"center"
		});

	};
	des.save = function(){

	};

	des.check_pro_marker = function(id){
		var item = $$('leftTree').item($$('leftTree').getSelected());
		window.setTimeout(function() {
			var result = des.pro.checkItem(item);
			des.pro.show(result);
			item = null;
			des.backup.set_config();
		}, 1);
	};
	$$('details').attachEvent("onAfterEditStop", des.check_pro_marker);
	$$('details').attachEvent("onAfterCheck",des.check_pro_marker);
	
	
	$$('events').attachEvent("onaftercheck", function(id,state){
		var selected = $$('leftTree').item($$('leftTree').getSelected());
		if (state === 1) {
			selected.events[id]=state;
			dhx.ui.property.prototype.types.ev_checkbox.show_wizard(id);
		} else
			if (typeof(selected.events[id]) !== 'undefined') {
				delete selected.events[id];
			}
		window.setTimeout(function() {
			des.backup.set_config();
		}, 1);
		des._was_changed = true;
	});

	$$('events').attachEvent("onAfterCheck", function(id,state){
		var selected = $$('leftTree').item($$('leftTree').getSelected());
		var item = this.item(id);
		var code = (item.code) ? item.code : 'alert(\'' + id + '\');';
		if (state === 1)
			selected.events[id]=code;
		else
			delete selected.events[id];
		des._was_changed = true;
	});

	$$('events').attachEvent("oncodesetted", function(id,code){
		var selected = $$('leftTree').item($$('leftTree').getSelected());
		selected.events[id]=code;
		des._was_changed = true;
		des.backup.set_config();
	});

	$$('details').attachEvent("OnGetTooltip", function(id) {
		this.tooltip = des.tooltipById(id);
	});

	$$('events').attachEvent("OnGetTooltip", function(id) {
		this.tooltip = des.tooltipById(id);
	});

	
	
	

	$$('details').attachEvent("onaftercheck", function(id, state){
		var tree = $$('leftTree');
		var treeid = this._linked_id;
		
		if (des.nesting._can_enable[id]){
			var exist_id = des.exists(tree, treeid, id);
				if (exist_id){
					des.change_content_confirm(id,treeid, function(result){
						var tree = $$('leftTree');
						var treeid = tree.getSelected();
						if(result){//if confirmed
							var value = $$('details').item(id).value;
							tree.remove(exist_id);
							des.property(tree, $$('details')._linked_id, id, value);
						}else{//if aborted
							tree.item(treeid)[id] = 1;
							des.refresh_details(treeid, tree.item(treeid));
						}
					});
				}else
						des.create(tree, treeid, id);
			}
    	des.property(tree, treeid, id, state);
	});

	$$('leftTree').attachEvent('onAfterSelect', function(id){
		var form = $$('details');
        form.stopEdit();
		var data = this.item(id);
		form._linked_id = id;
		des.fill_datasources();
		form.clearAll();
		form.parse(des.deep_copy(des.details[data.element]), "json");
		form.setValues(des.normalize(data));
        des.check_details_batch();
		des.check_body_elements();
		// get events list and load it into property
		var events_cfg = des.deep_copy(des.event_details[data.element])||[];
		if(events_cfg.length === 0){
			events_cfg.push(des.dont_have_public_events);
		}

		var events = $$('events');
		events.clearAll();
		events.parse(events_cfg);

		// set checked events
		var evs = {};
		for (var i in data.events)
			evs[i] = 1;
		events.setValues(evs);

		// set code for every checked event
		// it's because we are storing in value checkbox result - 0/1
		// and event callback code in item.code
		for (i in data.events) {
			var item = events.item(i);
			item.code = data.events[i];
			events.update(i, item);
		}
	});


	$$('leftTree').attachEvent('onBeforeSelect', function(id){
		 if($$('preview_mode').value == "Design"){
			des.activeControls.unselectPreviewControl();
		 }
	});
	//select control in preview area, when appropriate leftTree node is selected
	$$('leftTree').attachEvent('onAfterSelect', function(id){
		 if($$('preview_mode').value == "Design"){
			des.activeControls.selectPreviewControl(id);
		 }
	});


	des.sort_items = function(a, b) {
		if (a.value.toLowerCase() > b.value.toLowerCase())
			return 1;
		else if (a.value.toLowerCase() < b.value.toLowerCase())
			return -1;
		else
			return 0;
	};
	des.createContextMenu = function(id){
		var element = $$('leftTree').item(id).element;
        var contextMenuItems = [];
		var is_snippet = des.is_snippet(id);
		

		var in_main_ui = des.containing_one_per_page_elements("gui").length;
		var in_windows = des.containing_one_per_page_elements("windows").length;
	
        //items count
        var cnt = 0;
        for (var key in des.nesting[element])  {
			if(!(des.nesting._one_per_page[key] && (in_main_ui || in_windows)) || is_snippet){
				if (des.get_action_type(key))
				contextMenuItems.push({
					 id:key, value:des.protos[key]._text
				});
				cnt++;
			}
        }


		$$('options').clearAll();
		contextMenuItems.sort(des.sort_items);
		
		if(!contextMenuItems.length){
			return false;
		}
       
        $$('options').parse(contextMenuItems,'json');
		
        $$('main_context_menu').config.treeParent = id;
        $$('main_context_menu').resize();


		return true;
	};
    $$('leftTree').attachEvent("onBeforeContextMenu", function(id){
		$$('options').refresh();		
		return des.createContextMenu(id);
    });
	
    dhx.customized_context_menu = function(id){
		var selection = $$('leftTree').getSelected();
    	if (!selection) return false;
    	if (id == "child")
    		return des.createContextMenu(selection);
    	else{
			if($$('leftTree').item(selection).$parent)
				return des.createContextMenu($$('leftTree').item(selection).$parent);
			else
				return false;

		}
	};

	$$('help_bar').attachEvent("onItemClick", function(id,event) {
		if (id === 'child' || id === 'sibling') {
			$$('main_context_menu').setPosition(Math.floor($$(id).$view.offsetLeft), $$(id).$view.offsetTop + $$(id).$height);
			var sel = $$('leftTree').getSelected();
			if(!sel)
				return false;			
			$$('options').refresh();
			if(dhx.customized_context_menu(id))
				$$('main_context_menu').show();
			else
				return false;
		}
		return false;
	});





	$$('options').attachEvent("onItemClick", function(id){
    	this.getParent().hide();
		if(des.nesting.container[$$('leftTree').item(this.getParent().config.treeParent).element])
			var action = "add";
		else
			var action = des.get_action_type(id);
    	var tree = $$('leftTree');
    	var treeid = $$('main_context_menu').config.treeParent;
    	
    	if (action == "add"){
    		des.create(tree, treeid, id);
			
			if(id == "context_menu")
				des.update_context_menus();

			tree.refresh();

               
    	} else if (action == "enable"){
    		var exist_id = des.exists(tree, treeid, id);
			if (exist_id){
					des.change_content_confirm(id, treeid, function(result){
						if(result){
							tree.remove(exist_id);
							tree.item(treeid)[id] = 0;
							des.refresh_details(treeid,tree.item(treeid));
						}
					});
			}
			else
    			des.create(tree, treeid, id);
    		tree.refresh();
    	} else  if (action == "set"){
            //only shows if selected element's body already contains some component
            var selected = $$('leftTree').item(treeid);
            if(selected.body && selected.body != 'none'){
                 des.change_content_confirm('body', treeid, function(result){
					if(result){
						des.before_set(tree, treeid);
						des.create(tree, treeid, id);
						tree.refresh();
					}
    		 });
             }else{
				des.before_set(tree, treeid);
				des.create(tree, treeid, id);
				tree.refresh();
             }
    	} else
	    	dhx.alert(this.item(id).value +" "+this.getParent().getArea().id );
		des.backup.set_config();
		des.update_one_per_page();
	});


	var items = document.body.childNodes;

	for(var i in items){//hide default gray border
		if(items[i].className && items[i].className=="dhx_view dhx_window")
			items[i].style.borderTop="none";
	}
    des.leftTree = $$('leftTree');
    des.leftTree.parse(des.config.work,'json');
	des.backup.set_config();
    des.load_snippets();

    des._was_changed = true;
	
//##DESKTOP CODE HERE//

    window.setInterval(function(){
		if($$('is_preview').value == "on" && des.was_changed()){		
			des.preview();			
    	}
	}, 1000);
	$$('leftTree')._destructor = $$('leftTree').destructor;
	$$('leftTree').destructor = function(){des.save_snippets();$$('leftTree')._destructor();};

	des.share.load();
});
des.one_per_page = {

};
//DESKTOP CODE HERE##//


