!function(){"use strict";var e,t={113:function(e,t,n){var r=window.wp.blocks,o=window.wp.element,a=window.wp.i18n,i=window.wp.blockEditor,l=window.wp.serverSideRender,c=n.n(l),s=window.wp.components,u=JSON.parse('{"$schema":"https://json.schemastore.org/block.json","apiVersion":2,"name":"mrkwp/porta-place-map-search","version":"0.1.0","title":"Porta Places Map","category":"widgets","icon":"location","description":"Display Porta Places.","supports":{"html":false},"attributes":{"serviceArea":{"type":"string","default":""}},"textdomain":"porta-places","editorScript":"file:./index.js","editorStyle":"file:./index.css","style":"file:./style-index.css"}');(0,r.registerBlockType)(u,{edit:function(e){let{attributes:t,setAttributes:n}=e;const{serviceArea:r}=t,l=(0,i.useBlockProps)(),u=wp.data.select("core").getEntityRecords("taxonomy","gd_placecategory"),p=u?u.map((e=>({label:e.name,value:e.slug}))):[];return p.unshift({label:"Select an option",value:""}),(0,o.createElement)(o.Fragment,null,(0,o.createElement)(i.InspectorControls,null,(0,o.createElement)(s.PanelBody,{title:(0,a.__)("Category Settings","cvgt-locations"),initialOpen:!0},(0,o.createElement)(s.PanelRow,null,(0,o.createElement)("fieldset",null,(0,o.createElement)(s.SelectControl,{label:(0,a.__)("Select a Service Area","cvgt-locations"),options:p,value:r,onChange:e=>{n({serviceArea:e})}}))))),(0,o.createElement)("div",l,(0,o.createElement)(c(),{block:"mrkwp/porta-place-map-search",attributes:t})))},save:e=>null})}},n={};function r(e){var o=n[e];if(void 0!==o)return o.exports;var a=n[e]={exports:{}};return t[e](a,a.exports,r),a.exports}r.m=t,e=[],r.O=function(t,n,o,a){if(!n){var i=1/0;for(u=0;u<e.length;u++){n=e[u][0],o=e[u][1],a=e[u][2];for(var l=!0,c=0;c<n.length;c++)(!1&a||i>=a)&&Object.keys(r.O).every((function(e){return r.O[e](n[c])}))?n.splice(c--,1):(l=!1,a<i&&(i=a));if(l){e.splice(u--,1);var s=o();void 0!==s&&(t=s)}}return t}a=a||0;for(var u=e.length;u>0&&e[u-1][2]>a;u--)e[u]=e[u-1];e[u]=[n,o,a]},r.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return r.d(t,{a:t}),t},r.d=function(e,t){for(var n in t)r.o(t,n)&&!r.o(e,n)&&Object.defineProperty(e,n,{enumerable:!0,get:t[n]})},r.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},function(){var e={311:0,149:0};r.O.j=function(t){return 0===e[t]};var t=function(t,n){var o,a,i=n[0],l=n[1],c=n[2],s=0;if(i.some((function(t){return 0!==e[t]}))){for(o in l)r.o(l,o)&&(r.m[o]=l[o]);if(c)var u=c(r)}for(t&&t(n);s<i.length;s++)a=i[s],r.o(e,a)&&e[a]&&e[a][0](),e[a]=0;return r.O(u)},n=self.webpackChunkcvgt_gmap=self.webpackChunkcvgt_gmap||[];n.forEach(t.bind(null,0)),n.push=t.bind(null,n.push.bind(n))}();var o=r.O(void 0,[149],(function(){return r(113)}));o=r.O(o)}();