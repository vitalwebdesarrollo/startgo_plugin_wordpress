(()=>{"use strict";const e=window.React,t=window.wp.blocks,l=window.wp.blockEditor,r=window.wp.components,a=window.wp.i18n,o=window.wp.element,n=window.wp.data;(0,t.registerBlockType)("startgo-plugin/formulario-bloque",{title:(0,a.__)("Bloque de Formulario","startgo-plugin"),icon:"feedback",category:"widgets",attributes:{colorFondo:{type:"string",default:"#ffffff"},userNombre:{type:"string",default:""},userApellido:{type:"string",default:""},userEmail:{type:"string",default:""}},edit({attributes:t,setAttributes:i}){const{colorFondo:u,userNombre:s,userApellido:c,userEmail:m}=t,p=(0,l.useBlockProps)({style:{backgroundColor:u}});return(0,o.useEffect)((()=>{const e=(0,n.select)("core").getCurrentUser();e&&i({userNombre:e.first_name||"",userApellido:e.last_name||"",userEmail:e.email||""})}),[]),(0,e.createElement)(e.Fragment,null,(0,e.createElement)(l.InspectorControls,null,(0,e.createElement)(r.PanelBody,{title:(0,a.__)("Color de Fondo","startgo-plugin")},(0,e.createElement)(r.ColorPalette,{value:u,onChange:e=>i({colorFondo:e})}))),(0,e.createElement)("div",{...p},(0,e.createElement)("form",null,(0,e.createElement)("input",{type:"text",name:"nombre",placeholder:"Nombre",value:s,onChange:e=>i({userNombre:e.target.value})}),(0,e.createElement)("input",{type:"text",name:"apellido",placeholder:"Apellido",value:c,onChange:e=>i({userApellido:e.target.value})}),(0,e.createElement)("input",{type:"email",name:"email",placeholder:"Correo Electrónico",value:m,onChange:e=>i({userEmail:e.target.value})}),(0,e.createElement)("textarea",{name:"sugerencias",placeholder:"Sugerencias"}),(0,e.createElement)("select",{id:"pais",name:"pais"}),(0,e.createElement)("button",{type:"submit"},"Enviar"))))},save({attributes:t}){const{colorFondo:r,userNombre:a,userApellido:o,userEmail:n}=t,i=l.useBlockProps.save({style:{backgroundColor:r}});return(0,e.createElement)("div",{...i},(0,e.createElement)("form",null,(0,e.createElement)("input",{type:"text",name:"nombre",placeholder:"Nombre",defaultValue:a}),(0,e.createElement)("input",{type:"text",name:"apellido",placeholder:"Apellido",defaultValue:o}),(0,e.createElement)("input",{type:"email",name:"email",placeholder:"Correo Electrónico",defaultValue:n}),(0,e.createElement)("textarea",{name:"sugerencias",placeholder:"Sugerencias"}),(0,e.createElement)("select",{id:"pais",name:"pais"}),(0,e.createElement)("button",{type:"submit"},"Enviar")))}})})();