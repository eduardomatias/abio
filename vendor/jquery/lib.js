
	function formataDataBR(data){
	    var dia = data.getDate();
	    if (dia.toString().length == 1)
	      dia = "0"+dia;
	    var mes = data.getMonth()+1;
	    if (mes.toString().length == 1)
	      mes = "0"+mes;
	    var ano = data.getFullYear();
	    return dia+"/"+mes+"/"+ano;
	}

	function somaDataBR(data, qntDias){
		var dia = data.getDate();
		if (dia.toString().length == 1)
		  dia = "0"+dia;
		var mes = data.getMonth()+1;
		if (mes.toString().length == 1)
		  mes = "0"+mes;
		var ano = data.getFullYear();
		var diaAtualizado = 0;
		var mesAtualizado;

		if(qntDias > dia){
			diaAtualizado = '01';
			mesAtualizado = mes +1;
		}
		else{
			diaAtualizado = dia + qntDias;
			mesAtualizado = mes;
		}

		return diaAtualizado+"/"+mesAtualizado+"/"+ano;
	}

	function subtraiDataBR(data, qntDias){
		var dia = data.getDate();
		if (dia.toString().length == 1)
		  dia = "0"+dia;
		var mes = data.getMonth()+1;
		if (mes.toString().length == 1)
		  mes = "0"+mes;
		var ano = data.getFullYear();

		var diaAtualizado = 0;
		var mesAtualizado;
		if(qntDias > dia){
			diaAtualizado = '01';
			mesAtualizado = mes -1;
		}
		else{
			diaAtualizado = dia - qntDias;
			mesAtualizado = mes;
		}

		return diaAtualizado+"/"+mesAtualizado+"/"+ano;
	}

	function formataHoraBR(data){
	    var hora = data.getHours();
	    if (hora.toString().length == 1)
	      hora = "0"+hora;
	    var minuto = data.getMinutes();
	    if (minuto.toString().length == 1)
	      minuto = "0"+minuto;
	    return hora+":"+minuto;
	}

	// converte xml em obj JSON
	// function xml2json(xml) {
		// try {
			// var obj = {};
			// if (xml.children.length > 0) {
				// for (var i = 0; i < xml.children.length; i++) {
					// var item = xml.children.item(i);
					// var nodeName = item.nodeName;

					// if (typeof (obj[nodeName]) == "undefined") {
						// obj[nodeName] = xml2json(item);

					// } else {
						// if (typeof (obj[nodeName].push) == "undefined") {
							// var old = obj[nodeName];
							// obj[nodeName] = [];
							// obj[nodeName].push(old);

						// }
						// obj[nodeName].push(xml2json(item));
					// }
				// }
			// } else {
				// obj = xml.textContent;
			// }
		// return obj;
		// } catch (e) {
			// console.log(e.message);
		// }
	// }
	

function xml2json(xml, tab) {
   var X = {
      toObj: function(xml) {
         var o = {};
         if (xml.nodeType==1) {   // element node ..
            if (xml.attributes.length)   // element with attributes  ..
               for (var i=0; i<xml.attributes.length; i++)
                  o["@"+xml.attributes[i].nodeName] = (xml.attributes[i].nodeValue||"").toString();
            if (xml.firstChild) { // element has child nodes ..
               var textChild=0, cdataChild=0, hasElementChild=false;
               for (var n=xml.firstChild; n; n=n.nextSibling) {
                  if (n.nodeType==1) hasElementChild = true;
                  else if (n.nodeType==3 && n.nodeValue.match(/[^ \f\n\r\t\v]/)) textChild++; // non-whitespace text
                  else if (n.nodeType==4) cdataChild++; // cdata section node
               }
               if (hasElementChild) {
                  if (textChild < 2 && cdataChild < 2) { // structured element with evtl. a single text or/and cdata node ..
                     X.removeWhite(xml);
                     for (var n=xml.firstChild; n; n=n.nextSibling) {
                        if (n.nodeType == 3)  // text node
                           o["text"] = X.escape(n.nodeValue);
                        else if (n.nodeType == 4)  // cdata node
                           o["#cdata"] = X.escape(n.nodeValue);
                        else if (o[n.nodeName]) {  // multiple occurence of element ..
                           if (o[n.nodeName] instanceof Array)
                              o[n.nodeName][o[n.nodeName].length] = X.toObj(n);
                           else
                              o[n.nodeName] = [o[n.nodeName], X.toObj(n)];
                        }
                        else  // first occurence of element..
                           o[n.nodeName] = X.toObj(n);
                     }
                  }
                  else { // mixed content
                     if (!xml.attributes.length)
                        o = X.escape(X.innerXml(xml));
                     else
                        o["text"] = X.escape(X.innerXml(xml));
                  }
               }
               else if (textChild) { // pure text
                  if (!xml.attributes.length)
                     o = X.escape(X.innerXml(xml));
                  else
                     o["text"] = X.escape(X.innerXml(xml));
               }
               else if (cdataChild) { // cdata
                  if (cdataChild > 1)
                     o = X.escape(X.innerXml(xml));
                  else
                     for (var n=xml.firstChild; n; n=n.nextSibling)
                        o["#cdata"] = X.escape(n.nodeValue);
               }
            }
            if (!xml.attributes.length && !xml.firstChild) o = null;
         }
         else if (xml.nodeType==9) { // document.node
            o = X.toObj(xml.documentElement);
         }
         else
            alert("unhandled node type: " + xml.nodeType);
         return o;
      },
      toJson: function(o, name, ind) {
         var json = name ? ("\""+name+"\"") : "";
         if (o instanceof Array) {
            for (var i=0,n=o.length; i<n; i++)
               o[i] = X.toJson(o[i], "", ind+"\t");
            json += (name?":[":"[") + (o.length > 1 ? ("\n"+ind+"\t"+o.join(",\n"+ind+"\t")+"\n"+ind) : o.join("")) + "]";
         }
         else if (o == null)
            json += (name&&":") + "null";
         else if (typeof(o) == "object") {
            var arr = [];
            for (var m in o)
               arr[arr.length] = X.toJson(o[m], m, ind+"\t");
            json += (name?":{":"{") + (arr.length > 1 ? ("\n"+ind+"\t"+arr.join(",\n"+ind+"\t")+"\n"+ind) : arr.join("")) + "}";
         }
         else if (typeof(o) == "string")
            json += (name&&":") + "\"" + o.toString() + "\"";
         else
            json += (name&&":") + o.toString();
         return json;
      },
      innerXml: function(node) {
         var s = ""
         if ("innerHTML" in node)
            s = node.innerHTML;
         else {
            var asXml = function(n) {
               var s = "";
               if (n.nodeType == 1) {
                  s += "<" + n.nodeName;
                  for (var i=0; i<n.attributes.length;i++)
                     s += " " + n.attributes[i].nodeName + "=\"" + (n.attributes[i].nodeValue||"").toString() + "\"";
                  if (n.firstChild) {
                     s += ">";
                     for (var c=n.firstChild; c; c=c.nextSibling)
                        s += asXml(c);
                     s += "</"+n.nodeName+">";
                  }
                  else
                     s += "/>";
               }
               else if (n.nodeType == 3)
                  s += n.nodeValue;
               else if (n.nodeType == 4)
                  s += "<![CDATA[" + n.nodeValue + "]]>";
               return s;
            };
            for (var c=node.firstChild; c; c=c.nextSibling)
               s += asXml(c);
         }
         return s;
      },
      escape: function(txt) {
         return txt.replace(/[\\]/g, "\\\\")
                   .replace(/[\"]/g, '\\"')
                   .replace(/[\n]/g, '\\n')
                   .replace(/[\r]/g, '\\r');
      },
      removeWhite: function(e) {
         e.normalize();
         for (var n = e.firstChild; n; ) {
            if (n.nodeType == 3) {  // text node
               if (!n.nodeValue.match(/[^ \f\n\r\t\v]/)) { // pure whitespace text node
                  var nxt = n.nextSibling;
                  e.removeChild(n);
                  n = nxt;
               }
               else
                  n = n.nextSibling;
            }
            else if (n.nodeType == 1) {  // element node
               X.removeWhite(n);
               n = n.nextSibling;
            }
            else                      // any other node
               n = n.nextSibling;
         }
         return e;
      }
   };
   if (xml.nodeType == 9) // document node
      xml = xml.documentElement;
   var json = X.toJson(X.toObj(X.removeWhite(xml)), xml.nodeName, "\t");
   return "{\n" + tab + (tab ? json.replace(/\t/g, tab) : json.replace(/\t|\n/g, "")) + "\n}";
}


function exportarExcel(urlExcel) {
	$.ajax({
		type: "GET",
		url: urlExcel,
		success: function(response){
			window.location = urlExcel;
		},
		error: function(error){
			dhtmlx.alert({
				title:"Atenção!",
				type:"alert-error",
				text:"Não foi possível gerar o excel."
			});
		}
	});
}


