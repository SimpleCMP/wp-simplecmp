"use strict";var SimpleCMP=(()=>{var ye=Object.defineProperty;var mt=Object.getOwnPropertyDescriptor;var En=Object.getOwnPropertyNames;var Tn=Object.prototype.hasOwnProperty;var Mn=(r,e)=>{for(var t in e)ye(r,t,{get:e[t],enumerable:!0})},Rn=(r,e,t,n)=>{if(e&&typeof e=="object"||typeof e=="function")for(let i of En(e))!Tn.call(r,i)&&i!==t&&ye(r,i,{get:()=>e[i],enumerable:!(n=mt(e,i))||n.enumerable});return r};var Dn=r=>Rn(ye({},"__esModule",{value:!0}),r),h=(r,e,t,n)=>{for(var i=n>1?void 0:n?mt(e,t):e,o=r.length-1,s;o>=0;o--)(s=r[o])&&(i=(n?s(e,t,i):s(i))||i);return n&&i&&ye(e,t,i),i};var hr={};Mn(hr,{CmsBridge:()=>U,LayeredClassifier:()=>K,ServiceDbClient:()=>V,VERSION:()=>ir,addEventListener:()=>cr,audit:()=>er,auditChecks:()=>He,auditDom:()=>nr,auditWorstSeverity:()=>tr,getManager:()=>lr,getRecorder:()=>ar,init:()=>rr,show:()=>sr,updateConfig:()=>dr});function Ue(r=document){let e=Ln(r);return[Nn(e),Un(e),In(e),On(r)]}function vt(r,e){let t=r.getAttribute("aria-label");if(t!==null&&t.trim()!=="")return t.trim();let n=r.getAttribute("aria-labelledby");return n!==null&&n.trim()!==""?n.split(/\s+/).map(i=>e.querySelector(`[id="${i}"]`)?.textContent?.trim()??"").filter(i=>i!=="").join(" ").trim():""}function Pn(r,e){let t=vt(r,e);return t!==""?t:(r.textContent??"").trim()}function On(r){let e="dom-accessible-names",n="Banner region and actions have accessible names",i=Array.from(r.querySelectorAll("simplecmp-banner"));if(i.length===0)return{id:e,section:"2.2",severity:"info",title:n,detail:"No banner is currently mounted \u2014 DOM check skipped.",passed:!0};let o=[];for(let s of i){let a=s.shadowRoot;if(a===null)continue;let c=a.querySelector(".cn-body");c!==null&&vt(c,a)===""&&o.push("the banner region has no accessible name (add aria-label or aria-labelledby)"),Array.from(a.querySelectorAll(".cn-buttons > *")).forEach((f,p)=>{Pn(f,a)===""&&o.push(`banner action ${p+1} has no accessible name`)})}return o.length===0?{id:e,section:"2.2",severity:"info",title:n,detail:"The banner region and every action button expose an accessible name.",passed:!0}:{id:e,section:"2.2",severity:"critical",title:n,detail:`WCAG 4.1.2 / 2.4.6: assistive tech can't name part of the consent UI, so screen-reader users can't identify or operate it \u2014 consent that can't be perceived isn't valid. Unnamed:
  - ${o.join(`
  - `)}`,passed:!1}}function Ln(r){let e=r.querySelectorAll("simplecmp-banner"),t=[];for(let n of Array.from(e)){let i=n.shadowRoot;if(i===null)continue;let o=i.querySelectorAll(".cn-buttons > *");for(let s of Array.from(o))s instanceof HTMLElement&&t.push(s)}return t}function Nn(r){let e="dom-buttons-are-buttons",n="Banner actions are real button elements";if(r.length===0)return{id:e,section:"2.2",severity:"info",title:n,detail:"No banner is currently mounted \u2014 DOM check skipped.",passed:!0};let i=r.filter(o=>o.tagName!=="BUTTON").map(o=>o.tagName.toLowerCase());return i.length===0?{id:e,section:"2.2",severity:"info",title:n,detail:"All banner action elements are <button> tags.",passed:!0}:{id:e,section:"2.2",severity:"critical",title:n,detail:`Found ${i.length} action element(s) rendered as <${i.join(", ")}> instead of <button>. EDPB 03/2022 \xA7 Skipping flags reject-as-link as a deceptive design pattern \u2014 actions must be real buttons so visitors (and assistive tech) treat them as equivalent affordances. Don't override the bundle's button render.`,passed:!1}}function Un(r){let e="dom-buttons-equal-styling",n="Banner buttons are styled identically",[i,...o]=r;if(i===void 0||o.length===0)return{id:e,section:"1.2",severity:"info",title:n,detail:i===void 0?"No banner is currently mounted \u2014 DOM check skipped.":"Only one button rendered \u2014 equal-styling check requires at least two.",passed:!0};let s=ht(i),a=[];return o.forEach((c,l)=>{let f=ht(c);for(let p of Bn)f[p]!==s[p]&&a.push(`button ${l+2} ${p}: ${f[p]} (button 1: ${s[p]})`)}),a.length===0?{id:e,section:"1.2",severity:"info",title:n,detail:"All visible buttons share the same color, weight, and border styling.",passed:!0}:{id:e,section:"1.2",severity:"critical",title:n,detail:`Banner buttons differ in styling beyond their labels \u2014 this is a Stirring dark pattern (BGH I ZR 7/16, DSB D124.0507/24, EDPB 03/2022). Buttons must share identical color, weight and border treatment. Mismatched properties:
  - ${a.slice(0,6).join(`
  - `)}${a.length>6?`
  - (\u2026 ${a.length-6} more)`:""}`,passed:!1}}function In(r){let e="dom-buttons-wcag-contrast",n="Banner buttons meet WCAG AA contrast (\u2265 4.5:1)";if(r.length===0)return{id:e,section:"1.2",severity:"info",title:n,detail:"No banner is currently mounted \u2014 DOM check skipped.",passed:!0};let i=[];return r.forEach((o,s)=>{let a=getComputedStyle(o),c=yt(a.color),l=jn(o);if(c===null||l===null)return;let f=zn(c,l);f<4.5&&i.push(`button ${s+1}: contrast ${f.toFixed(2)}:1 (needs \u2265 4.5:1)`)}),i.length===0?{id:e,section:"1.2",severity:"info",title:n,detail:"All button text meets WCAG AA contrast against its background.",passed:!0}:{id:e,section:"1.2",severity:"critical",title:n,detail:`One or more banner buttons fall below the WCAG AA text-contrast threshold of 4.5:1. The Austrian DSB (D124.0507/24) explicitly cited insufficient contrast as a manipulative design choice. Failing buttons:
  - ${i.join(`
  - `)}`,passed:!1}}var Bn=["backgroundColor","color","fontWeight","borderTopWidth","borderTopStyle","borderTopColor"];function ht(r){let e=getComputedStyle(r);return{backgroundColor:e.backgroundColor,color:e.color,fontWeight:e.fontWeight,borderTopWidth:e.borderTopWidth,borderTopStyle:e.borderTopStyle,borderTopColor:e.borderTopColor}}function jn(r){let e=r;for(;e!==null;){let t=getComputedStyle(e),n=yt(t.backgroundColor);if(n!==null&&!Hn(t.backgroundColor))return n;let i=e.parentNode??e.getRootNode().host??null;e=i instanceof HTMLElement?i:null}return[255,255,255]}function yt(r){let e=r.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/);return e===null?null:[Number(e[1]),Number(e[2]),Number(e[3])]}function Hn(r){if(r==="transparent")return!0;let e=r.match(/rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*,\s*(\d*\.?\d+)\s*\)/);return e!==null&&Number(e[1])===0}function gt([r,e,t]){let n=i=>{let o=i/255;return o<=.03928?o/12.92:((o+.055)/1.055)**2.4};return .2126*n(r)+.7152*n(e)+.0722*n(t)}function zn(r,e){let t=gt(r),n=gt(e),[i,o]=t>n?[t,n]:[n,t];return(i+.05)/(o+.05)}var qn={de:[{phrase:"vielleicht sp\xE4ter",reason:"verschiebt die Entscheidung statt sie zu treffen"},{phrase:"nicht jetzt",reason:"klingt aufschiebend, nicht ablehnend"},{phrase:"\xFCberspringen",reason:'klingt nach \u201Esp\xE4ter", nicht \u201Enein"'},{phrase:"schlie\xDFen",reason:"beschreibt eine UI-Aktion, nicht eine Ablehnung"},{phrase:"weiter ohne",reason:"unklar \u2014 was genau wird abgelehnt?"}],en:[{phrase:"maybe later",reason:"defers instead of refuses"},{phrase:"not now",reason:"sounds like postponement, not refusal"},{phrase:"skip",reason:'reads as "later", not "no"'},{phrase:"close",reason:"describes a UI action, not a rejection"},{phrase:"continue without",reason:"ambiguous \u2014 what is actually being rejected?"},{phrase:"remind me later",reason:"defers instead of refuses"}],fr:[{phrase:"plus tard",reason:"reporte au lieu de refuser"},{phrase:"pas maintenant",reason:"sonne comme un report, pas un refus"},{phrase:"fermer",reason:"d\xE9crit une action UI, pas un refus"}],it:[{phrase:"pi\xF9 tardi",reason:"rimanda invece di rifiutare"},{phrase:"non ora",reason:"suona come un rinvio, non un rifiuto"},{phrase:"chiudi",reason:"descrive un'azione UI, non un rifiuto"}],es:[{phrase:"m\xE1s tarde",reason:"aplaza en lugar de rechazar"},{phrase:"ahora no",reason:"suena como aplazamiento, no rechazo"},{phrase:"cerrar",reason:"describe una acci\xF3n UI, no un rechazo"}],nl:[{phrase:"later",reason:"verschuift de keuze in plaats van te weigeren"},{phrase:"niet nu",reason:"klinkt als uitstel, geen weigering"},{phrase:"sluiten",reason:"beschrijft een UI-actie, geen weigering"},{phrase:"overslaan",reason:'klinkt als "later", niet "nee"'}]},Fn={de:[{phrase:"erlebnis verbessern",reason:'\u201EErlebnis" ist Marketing-Sprache'},{phrase:"verbessere dein erlebnis",reason:"manipulativer Nudge zur Zustimmung"},{phrase:"verbessern sie ihr erlebnis",reason:"manipulativer Nudge zur Zustimmung"},{phrase:"volle funktionalit\xE4t",reason:"suggeriert eingeschr\xE4nkten Service bei Ablehnung"},{phrase:"volles erlebnis",reason:"suggeriert eingeschr\xE4nkten Service bei Ablehnung"},{phrase:"vertrauensvolle partner",reason:"vage \u2014 benenne die Verantwortlichen"},{phrase:"optimal erleben",reason:'\u201Eoptimal" ist subjektiv und manipulativ'},{phrase:"personalisieren sie ihren besuch",reason:"Marketing-Nudge zur Zustimmung"},{phrase:"personalisiere deinen besuch",reason:"Marketing-Nudge zur Zustimmung"}],en:[{phrase:"improve your experience",reason:"marketing nudge toward acceptance"},{phrase:"enhance your experience",reason:"marketing nudge toward acceptance"},{phrase:"get the full experience",reason:"suggests degraded service on refusal"},{phrase:"full functionality",reason:"suggests degraded service on refusal"},{phrase:"trusted partners",reason:"vague \u2014 name the controllers"},{phrase:"personalize your visit",reason:"marketing nudge toward acceptance"},{phrase:"continue to enjoy",reason:"marketing nudge"},{phrase:"tailored experience",reason:"marketing nudge toward acceptance"}],fr:[{phrase:"meilleure exp\xE9rience",reason:"langage marketing, pousse vers l\u2019acceptation"},{phrase:"exp\xE9rience optimale",reason:"\xAB optimal \xBB est subjectif et manipulateur"},{phrase:"partenaires de confiance",reason:"vague \u2014 nommer les responsables"},{phrase:"personnaliser votre visite",reason:"nudge marketing vers l\u2019acceptation"}],it:[{phrase:"migliore esperienza",reason:"linguaggio marketing, spinge verso l\u2019accettazione"},{phrase:"esperienza ottimale",reason:"\xABottimale\xBB \xE8 soggettivo e manipolativo"},{phrase:"partner di fiducia",reason:"vago \u2014 nominare i titolari"},{phrase:"personalizza la tua visita",reason:"nudge marketing verso l\u2019accettazione"}],es:[{phrase:"mejor experiencia",reason:"lenguaje marketing, empuja hacia la aceptaci\xF3n"},{phrase:"experiencia \xF3ptima",reason:"\xAB\xF3ptima\xBB es subjetiva y manipuladora"},{phrase:"socios de confianza",reason:"vago \u2014 nombrar los responsables"},{phrase:"personalizar tu visita",reason:"nudge marketing hacia la aceptaci\xF3n"}],nl:[{phrase:"betere ervaring",reason:"marketingtaal, duwt richting acceptatie"},{phrase:"optimale ervaring",reason:'"optimaal" is subjectief en manipulatief'},{phrase:"vertrouwde partners",reason:"vaag \u2014 noem de verwerkingsverantwoordelijken"},{phrase:"personaliseer je bezoek",reason:"marketingnudge richting acceptatie"}]};function je(r,e){let t=r.translations;if(typeof t!="object"||t===null)return[];let n=[];for(let[i,o]of Object.entries(t)){if(typeof o!="object"||o===null)continue;let s=o;for(let a of e){if(typeof s!="object"||s===null){s=void 0;break}s=s[a]}typeof s=="string"&&s.trim()!==""&&n.push({lang:i,text:s})}return n}function bt(r,e,t){let n=r[e.toLowerCase()];if(n===void 0)return null;let i=t.toLowerCase();for(let o of n)if(i.includes(o.phrase))return o;return null}function kt(r){let e=je(r,["decline"]),t=[];for(let{lang:n,text:i}of e){let o=bt(qn,n,i);o!==null&&t.push(`[${n}] "${i}" \u2014 ${o.reason}`)}return t.length===0?{passed:!0,detail:"Reject button labels read as clear refusal across all configured languages."}:{passed:!1,detail:`Reject button label(s) read as deferral or postponement instead of refusal, which EDPB 03/2022 \xA7 Skipping treats as deceptive design. Affected language(s):
  - ${t.join(`
  - `)}`}}var Ie=80,Be=600;function wt(r){let e=je(r,["consentNotice","description"]),t=[];for(let{lang:n,text:i}of e){let o=i.length;o<Ie?t.push(`[${n}] ${o} chars \u2014 below the ${Ie}-char floor; likely doesn\u2019t carry the purpose enumeration + controller info "informed" consent needs.`):o>Be&&t.push(`[${n}] ${o} chars \u2014 above the ${Be}-char ceiling; risks EDPB 03/2022 \xA7 Overloading. Trim to the essentials or move detail into the modal.`)}return t.length===0?{passed:!0,detail:"Banner descriptions sit in the informative-but-readable range across languages."}:{passed:!1,detail:`Banner description length is outside the recommended range (${Ie}\u2013${Be} chars). Affected language(s):
  - ${t.join(`
  - `)}`}}function St(r){let e=je(r,["consentNotice","description"]),t=[];for(let{lang:n,text:i}of e){let o=bt(Fn,n,i);o!==null&&t.push(`[${n}] "${o.phrase}" \u2014 ${o.reason}`)}return t.length===0?{passed:!0,detail:"Banner descriptions stay clear of marketing-nudge language."}:{passed:!1,detail:`Banner description(s) contain marketing-nudge phrases that push the visitor toward accepting \u2014 EDPB 03/2022 \xA7 Stirring + CNIL 17.09.2020 flag this as deceptive design. Affected phrase(s):
  - ${t.join(`
  - `)}`}}var He=[{id:"privacy-policy-url",section:"1.5",title:"Privacy-policy URL configured",failSeverity:"critical",run:r=>{let e=r.privacyPolicy;return typeof e=="string"&&e!==""&&e!=="#"?{passed:!0,detail:"Privacy policy URL is set."}:typeof e=="object"&&e!==null&&Object.values(e).filter(n=>typeof n=="string"&&n!==""&&n!=="#").length>0?{passed:!0,detail:"Privacy policy URL is set (per-language map)."}:{passed:!1,detail:"GDPR Art. 13 requires the privacy policy to be linked before consent is captured. Set `simplecmp.privacyPolicy` to the live URL of the site\u2019s policy."}}},{id:"first-layer-reject",section:"1.3",title:'"Reject all" available on first layer',failSeverity:"critical",run:r=>r.hideDeclineAll===!0?{passed:!1,detail:"VG Hannover 10 A 5385/22 (19.03.2025) and the EDPB Cookie Banner Taskforce require a first-layer Reject affordance. `hideDeclineAll: true` removes it. Set `hideDeclineAll: false` or remove the property."}:{passed:!0,detail:"Reject affordance is present on the first banner layer."}},{id:"opt-in-defaults",section:"1.1",title:"Non-essential services default to OFF",failSeverity:"critical",run:r=>{let e=r.services??[],t=[];for(let n of e)n.required!==!0&&n.default===!0&&t.push(n.name);return t.length===0?{passed:!0,detail:"All non-essential services default to OFF."}:{passed:!1,detail:`${t.length} non-essential service(s) have \`default: true\` (pre-consent granted): ${t.slice(0,5).join(", ")}${t.length>5?`, \u2026 (${t.length-5} more)`:""}. Pre-ticked consent fails Planet49 / Cookie II. Either mark the service \`required: true\` (if it truly is essential) or remove the \`default: true\` flag.`}}},{id:"pre-consent-blocking",section:"1.7",title:"Pre-consent tracking blocked",failSeverity:"critical",run:r=>{let e=r.interceptRuntime;return e===void 0||e===!1?{passed:!1,detail:"Without `interceptRuntime`, third-party scripts can dispatch requests before the user has chosen \u2014 \xA7 25 TDDDG and Art. 5(3) ePrivacy require prior consent for any non-essential storage/access. Enable `interceptRuntime: true` (or the CMS-side equivalent setting that does the same)."}:{passed:!0,detail:"Pre-consent runtime blocking is enabled."}}},{id:"persistent-revocation-trigger",section:"1.6",title:"Persistent revocation trigger enabled",failSeverity:"warning",run:r=>{let e=r.floatingTrigger;return e===void 0||e===!1?{passed:!1,detail:"GDPR Art. 7(3) demands that withdrawal be as easy as granting consent. Without a persistent trigger (`floatingTrigger`), visitors must hunt for the consent banner to revoke. Set `floatingTrigger: true` or configure a custom trigger."}:{passed:!0,detail:"Persistent revocation trigger is enabled."}}},{id:"imprint-url-dach",section:"1.5",title:"Imprint URL configured (DACH compliance)",failSeverity:"warning",run:r=>{let e=r.imprint;return typeof e=="string"&&e!==""&&e!=="#"?{passed:!0,detail:"Imprint URL is set."}:typeof e=="object"&&e!==null&&Object.values(e).filter(n=>typeof n=="string"&&n!==""&&n!=="#").length>0?{passed:!0,detail:"Imprint URL is set (per-language map)."}:{passed:!1,detail:"German TMG / Austrian ECG / Swiss UWG require a separately reachable Impressum. Surface the link next to the privacy policy in the banner by setting `simplecmp.imprint` to the live URL. Skip this check only if the site is not targeted at DACH visitors."}}},{id:"heuristic-decline-label-clarity",section:"2.2",title:"Reject button labels read as clear refusal",failSeverity:"warning",run:kt},{id:"heuristic-no-marketing-nudge-in-description",section:"2.3",title:"Banner description avoids marketing nudges",failSeverity:"warning",run:St},{id:"heuristic-description-length",section:"2.1",title:"Banner description sits in a readable length range",failSeverity:"warning",run:wt},{id:"services-have-purposes",section:"1.4",title:"Each service declares processing purposes",failSeverity:"warning",run:r=>{let e=r.services??[],t=[];for(let n of e){let i=n.purposes;(!Array.isArray(i)||i.length===0)&&t.push(n.name)}return t.length===0?{passed:!0,detail:"All services declare at least one purpose."}:{passed:!1,detail:`${t.length} service(s) have no \`purposes\` declared: ${t.slice(0,5).join(", ")}${t.length>5?`, \u2026 (${t.length-5} more)`:""}. EDPB 05/2020 \xA7 42 requires consent to be specific per purpose. Either tag each service with at least one purpose category or remove unused entries.`}}}];function _t(r){return He.map(e=>{let t=e.run(r);return{id:e.id,section:e.section,severity:t.passed?"info":e.failSeverity,title:e.title,detail:t.detail,passed:t.passed}})}function Ct(r){let e="info";for(let t of r){if(t.severity==="critical")return"critical";t.severity==="warning"&&(e="warning")}return e}var Wn="opt-in",Vn=new Set(["AT","BE","BG","HR","CY","CZ","DK","EE","FI","FR","DE","GR","HU","IE","IT","LV","LT","LU","MT","NL","PL","PT","RO","SK","SI","ES","SE","IS","LI","NO","GB","CH","EU","EEA"]),Gn=new Set(["US","US-CA","US-VA","US-CO","US-CT","US-UT","US-TX","US-OR","US-MT","US-DE","US-IA","US-NE","US-NH","US-NJ","US-IN","US-TN","US-MN","US-MD","US-KY","US-RI"]);function be(r,e,t=Wn){if(r){let n=r.toUpperCase();if(e){let i=e[r]??e[n];if(i)return i}if(Vn.has(n))return"opt-in";if(Gn.has(n))return"opt-out"}return t}function ze(){if(typeof document>"u")return[];let r=document.cookie.split(";"),e=[],t=/^\s*([^=]+)\s*=\s*(.*?)$/;for(let n of r){let i=t.exec(n);i!==null&&e.push({name:i[1]??"",value:i[2]??""})}return e}function qe(r){for(let e of ze())if(e.name===r)return e;return null}function xt(r,e,t,n,i){if(typeof document>"u")return;let o="";if(t){let s=new Date;s.setTime(s.getTime()+t*24*60*60*1e3),o=`; expires=${s.toUTCString()}`}n!==void 0&&(o+=`; domain=${n}`),o+=i!==void 0?`; path=${i}`:"; path=/",document.cookie=`${r}=${e||""}${o}; SameSite=Lax`}function Z(r,e,t){if(typeof document>"u")return!1;let n=`${r}=; Max-Age=-99999999;`;return document.cookie=n,n+=` path=${e||"/"};`,document.cookie=n,t!==void 0&&(n+=` domain=${t};`,document.cookie=n),qe(r)===null}var Fe=class{constructor(){this.value=null}get(){return this.value}set(e){this.value=e}delete(){this.value=null}},We=class{constructor(e){this.cookieName=e.storageName,this.cookieDomain=e.cookieDomain,this.cookiePath=e.cookiePath,this.cookieExpiresAfterDays=e.cookieExpiresAfterDays}get(){let e=qe(this.cookieName);return e?e.value:null}set(e){xt(this.cookieName,e,this.cookieExpiresAfterDays,this.cookieDomain,this.cookiePath)}delete(){Z(this.cookieName)}},ke=class{constructor(e,t){this.key=e.storageName,this.handle=t}get(){return this.handle.getItem(this.key)}getWithKey(e){return this.handle.getItem(e)}set(e){this.handle.setItem(this.key,e)}setWithKey(e,t){this.handle.setItem(e,t)}delete(){this.handle.removeItem(this.key)}deleteWithKey(e){this.handle.removeItem(e)}},Ve=class extends ke{constructor(e){super(e,localStorage)}},ee=class extends ke{constructor(e){super(e,sessionStorage)}},Kn={cookie:We,test:Fe,localStorage:Ve,sessionStorage:ee},Ge=Kn;function At(r){let e={};for(let t of Array.from(r.attributes))t.name.startsWith("data-")&&(e[t.name.slice(5)]=t.value);return e}function $t(r,e){for(let[t,n]of Object.entries(r))e[t]!==n&&e.setAttribute(`data-${t}`,n)}function Jn(r){return r.replace(/[-[\]/{}()*+?.\\^$|]/g,"\\$&")}function Et(r,e){return r===void 0?void 0:typeof r=="function"?r(e):new Function("opts",r)(e)}var te=class{constructor(e,t,n){this.confirmed=!1;this.changed=!1;this.states={};this.initialized={};this.executedOnce={};this.watchers=new Set;this.config=e;let i={storageName:this.storageName,cookieDomain:this.cookieDomain,cookiePath:this.cookiePath,cookieExpiresAfterDays:this.cookieExpiresAfterDays};if(t!==void 0)this.store=t;else{let o=Ge[this.storageMethod]??Ge.cookie;this.store=new o(i)}this.auxiliaryStore=n??new ee(i),this.consents=this.defaultConsents,this.loadConsents(),this.applyConsents(),this.savedConsents={...this.consents}}get storageMethod(){return this.config.storageMethod??"cookie"}get storageName(){return this.config.storageName??this.config.cookieName??"simplecmp"}get cookieDomain(){return this.config.cookieDomain}get cookiePath(){return this.config.cookiePath}get cookieExpiresAfterDays(){return this.config.cookieExpiresAfterDays??120}get defaultConsents(){let e={};for(let t of this.config.services)e[t.name]=this.getDefaultConsent(t);return e}watch(e){this.watchers.add(e)}unwatch(e){this.watchers.delete(e)}notify(e,t){for(let n of this.watchers)n.update(this,e,t)}getService(e){return this.config.services.find(t=>t.name===e)}get regime(){return be(this.config.region,this.config.regimes,this.config.regimeDefault)}get bannerMode(){let e=this.regime;return e==="opt-in"?"wall":e==="opt-out"?"notice":"none"}getDefaultConsent(e){if(e.required??this.config.required??!1)return!0;if(this.config.respectGPC!==!1&&typeof navigator<"u"&&navigator.globalPrivacyControl===!0)return!1;let n=this.regime!=="opt-in";return e.default??this.config.default??n}changeAll(e){let t=0;for(let n of this.config.services.filter(i=>!i.contextualConsentOnly)){let o=n.required??this.config.required??!1?!0:e;this.updateConsent(n.name,o)&&t++}return t}updateConsent(e,t){let n=(this.consents[e]||!1)!==t;return this.consents[e]=t,this.notify("consents",this.consents),n}resetConsents(){this.consents=this.defaultConsents,this.states={},this.confirmed=!1,this.applyConsents(),this.savedConsents={...this.consents},this.store.delete(),this.notify("consents",this.consents)}getConsent(e){return this.consents[e]||!1}loadConsents(){let e=this.store.get();if(e===null)return this.consents;let t;try{t=JSON.parse(decodeURIComponent(e))}catch{return this.consents=this.defaultConsents,this.consents}let n,i;if(t!==null&&typeof t=="object"&&"__v"in t&&"consents"in t){let s=t;n=s.__v,i=s.consents}else i=t;if(typeof i!="object"||i===null||Array.isArray(i))return this.consents=this.defaultConsents,this.consents;this.consents=i;let o=this.config.consentVersion;return o!==void 0&&n!==void 0&&!this._versionsCompatible(n,o)?(this.versionMismatch={storedVersion:n,configVersion:o,policy:this.config.consentVersionPolicy??"any"},this.consents=this.defaultConsents,this.confirmed=!1,this.changed=!0):(this._checkConsents(),this.notify("consents",this.consents)),this.consents}_versionsCompatible(e,t){let n=String(e),i=String(t);return(this.config.consentVersionPolicy??"any")==="major"?n.split(".")[0]===i.split(".")[0]:n===i}saveAndApplyConsents(e){this.saveConsents(e),this.applyConsents()}changedConsents(){let e={};for(let[t,n]of Object.entries(this.consents))this.savedConsents[t]!==n&&(e[t]=n);return e}saveConsents(e){let t=this.config.consentVersion!==void 0?{__v:this.config.consentVersion,consents:this.consents}:this.consents,n=encodeURIComponent(JSON.stringify(t));this.store.set(n),this.confirmed=!0,this.changed=!1,this.versionMismatch=void 0;let i=this.changedConsents();this.savedConsents={...this.consents},this.notify("saveConsents",{changes:i,consents:this.consents,type:e??"script"})}applyConsents(e,t,n){let i=0;for(let o of this.config.services){if(n!==void 0&&n!==o.name)continue;let s=o.vars??{},a={service:o,config:this.config,vars:s};this.initialized[o.name]||(this.initialized[o.name]=!0,Et(o.onInit,a))}for(let o of this.config.services){if(n!==void 0&&n!==o.name)continue;let s=this.states[o.name],a=o.vars??{},c=o.optOut!==void 0?o.optOut:this.config.optOut??!1,l=o.required!==void 0?o.required:this.config.required??!1,f=this.confirmed||c||e||t||!1,p=this.getConsent(o.name)&&f||l,m={service:o,config:this.config,vars:a,consents:this.consents,confirmed:this.confirmed};s!==p&&i++,!e&&(Et(p?o.onAccept:o.onDecline,m),this.updateServiceElements(o,p),this.updateServiceStorage(o,p),o.callback!==void 0&&o.callback(p,o),this.config.callback!==void 0&&this.config.callback(p,o),this.states[o.name]=p)}if(!e&&typeof document<"u"){let o=new Set(this.config.services.map(a=>a.name)),s=new Set;for(let a of Array.from(document.querySelectorAll("[data-name]"))){let c=a.getAttribute("data-name");if(c===null||c===""||o.has(c)||s.has(c)||n!==void 0&&n!==c)continue;s.add(c);let l={name:c,purposes:[]},f=this.getConsent(c);this.updateServiceElements(l,f)}}return this.notify("applyConsents",{changedServices:i,serviceName:n}),i}updateServiceElements(e,t){if(typeof document>"u")return;if(t){if(e.onlyOnce&&this.executedOnce[e.name])return;this.executedOnce[e.name]=!0}let n=document.querySelectorAll(`[data-name='${e.name}']`);for(let i of Array.from(n)){let o=i.parentElement;if(!o)continue;let s=At(i),{type:a,src:c,href:l}=s,f=["href","src","type"];if(a==="placeholder"){t?(i.style.display="none",s["original-display"]=i.style.display):i.style.display=s["original-display"]||"block";continue}if(i.tagName==="IFRAME"){if(t&&i.src===c){console.debug(`Skipping ${i.tagName} for service ${e.name}, as it already has the correct type...`);continue}let m=document.createElement(i.tagName);for(let g of Array.from(i.attributes))if(g.name==="style"){let[_="",$n=""]=g.value.split(":");m.style[_.trim()]=$n.trim()}else m.setAttribute(g.name,g.value);m.innerText=i.innerText,m.text=i.text,t?(s["original-display"]!==void 0&&(m.style.display=s["original-display"]),s.src!==void 0&&(m.src=s.src)):(m.src="about:blank",s["modified-by-klaro"]!==void 0&&s["original-display"]!==void 0?m.setAttribute("data-original-display",s["original-display"]):(i.style.display!==void 0&&m.setAttribute("data-original-display",i.style.display),m.setAttribute("data-modified-by-klaro","yes")),m.style.display="none"),o.insertBefore(m,i),o.removeChild(i),this._toggleAutoPlaceholder(m,e,t)}else if(i.tagName==="SCRIPT"||i.tagName==="LINK"){let p=i;if(t&&p.type===(a??"")&&p.src===c&&p.href===l){console.debug(`Skipping ${i.tagName} for service ${e.name}, as it already has the correct type / src / href...`);continue}let m=document.createElement(i.tagName);for(let g of Array.from(i.attributes))m.setAttribute(g.name,g.value);if(i.hasAttribute("nonce")&&m.setAttribute("nonce",i.nonce??""),m.innerText=i.innerText,m.text=i.text,t)m.type=a??"",c!==void 0&&(m.src=c),l!==void 0&&(m.href=l);else if(m.type="text/plain",i.tagName==="LINK"){let g=m.getAttribute("href");g!==null&&g!==""&&(m.hasAttribute("data-href")||m.setAttribute("data-href",g),m.removeAttribute("href"))}o.insertBefore(m,i),o.removeChild(i),this._toggleAutoPlaceholder(m,e,t)}else{let p=i;if(t){for(let m of f){let g=s[m];g!==void 0&&(s[`original-${m}`]===void 0&&(s[`original-${m}`]=p[m]??""),p[m]=g)}s.title!==void 0&&(i.title=s.title),s["original-display"]!==void 0?i.style.display=s["original-display"]:i.style.removeProperty("display")}else{s.title!==void 0&&i.removeAttribute("title"),s["original-display"]===void 0&&i.style.display!==void 0&&(s["original-display"]=i.style.display),i.style.display="none";for(let m of f)s[m]!==void 0&&(s[`original-${m}`]!==void 0?p[m]=s[`original-${m}`]:i.removeAttribute(m))}$t(s,i),this._toggleAutoPlaceholder(i,e,t)}}}_toggleAutoPlaceholder(e,t,n){if(typeof document>"u")return;let i=e.nextElementSibling,o=i?.hasAttribute("data-simplecmp-auto-placeholder")&&i.getAttribute("data-simplecmp-for")===t.name?i:null;if(n){o!==null&&o.remove();return}if(this.config.autoContextualPlaceholder===!1||t.noAutoPlaceholder===!0||e.hasAttribute("data-no-placeholder")||o!==null)return;let s=document.createElement("simplecmp-contextual-notice");s.setAttribute("service-name",t.name),s.setAttribute("data-simplecmp-auto-placeholder",""),s.setAttribute("data-simplecmp-for",t.name);let a=e.getAttribute("data-blocked-source");a!==null&&s.setAttribute("data-blocked-source",a);for(let l of["data-simplecmp-title","data-simplecmp-description"]){let f=e.getAttribute(l);f!==null&&s.setAttribute(l,f)}let c=s;c.serviceName=t.name,c.manager=this,c.config=this.config,e.insertAdjacentElement("afterend",s)}updateServiceStorage(e,t){if(t||!e.cookies||e.cookies.length===0||typeof window>"u"||typeof document>"u")return;let n=ze();for(let i of e.cookies){let o=i,s,a;if(Array.isArray(o))[o,s,a]=o;else if(o!==null&&typeof o=="object"&&!(o instanceof RegExp)){let l=o;o=l.pattern,s=l.path,a=l.domain}if(o===void 0)continue;let c;if(o instanceof RegExp)c=o;else if(typeof o=="string")c=o.startsWith("^")?new RegExp(o):new RegExp(`^${Jn(o)}$`);else continue;for(let l of n){if(c.exec(l.name)===null)continue;console.debug("Deleting cookie:",l.name,"Matched pattern:",c,"Path:",s,"Domain:",a);let f=Z(l.name,s,a);!f&&a===void 0&&(f=Z(l.name,s,`.${window.location.hostname}`)),f||console.warn(`SimpleCMP: cookie "${l.name}" still present after deletion attempt for service "${e.name}". It may be set on a path/domain we cannot reach from JS, or another script re-set it.`)}}}_checkConsents(){let e=!0,t=new Set(this.config.services.map(i=>i.name)),n=new Set(Object.keys(this.consents));for(let i of Object.keys(this.consents))t.has(i)||delete this.consents[i];for(let i of this.config.services)n.has(i.name)||(this.consents[i.name]=this.getDefaultConsent(i),e=!1);this.confirmed=e,e||(this.changed=!0)}};var Tt={acceptAll:"Alles akzeptieren",acceptSelected:"Auswahl akzeptieren",consentModal:{description:"Hier kannst du die Dienste, die wir auf dieser Website nutzen m\xF6chten, bewerten und anpassen. Du hast das Sagen! Aktiviere oder deaktiviere die Dienste, wie du es f\xFCr richtig h\xE4ltst.",privacyPolicy:{text:"Um mehr zu erfahren, lies bitte unsere {privacyPolicy}."}},consentNotice:{changeDescription:"Seit deinem letzten Besuch gab es \xC4nderungen, bitte erneuere deine Zustimmung.",description:"Hi! K\xF6nnen wir bitte ein paar zus\xE4tzliche Dienste f\xFCr {purposes} aktivieren? Du kannst deine Zustimmung sp\xE4ter jederzeit \xE4ndern oder zur\xFCckziehen.",learnMore:"Lass mich w\xE4hlen"},contextualConsent:{description:"M\xF6chtest du von {title} bereitgestellte externe Inhalte laden?",descriptionEmptyStore:"Um diesem Dienst dauerhaft zustimmen zu k\xF6nnen, musst du {title} in den {link} zustimmen.",descriptionUnknownHost:"Blockierter Drittinhalt von {title}. Diese Quelle wurde vom Site-Administrator noch nicht freigegeben \u2014 bitte wende dich an die Administratorin oder den Administrator, um diese Inhalte zu aktivieren."},decline:"Nein danke",ok:"Passt f\xFCr mich",privacyPolicy:{text:"Um mehr zu erfahren, lies bitte unsere {privacyPolicy}."},purposes:{advertising:{description:"Diese Dienste verarbeiten pers\xF6nliche Informationen, um dir personalisierte oder interessenbezogene Werbung zu zeigen."},analytics:{description:"Diese Dienste erfassen, wie Besucher diese Seite nutzen, damit wir ihre Funktion messen und verbessern k\xF6nnen."},marketing:{description:"Diese Dienste verarbeiten pers\xF6nliche Daten, um dir relevante Inhalte \xFCber Produkte, Dienstleistungen oder Themen zu zeigen, die dich interessieren k\xF6nnten."},personalization:{description:"Diese Dienste passen die Inhalte dieser Seite an deine Vorlieben und dein bisheriges Verhalten an."}},service:{disableAll:{description:"Mit diesem Schalter kannst du alle Dienste aktivieren oder deaktivieren."},optOut:{description:"Diese Dienste werden standardm\xE4\xDFig geladen (du kannst dich jedoch abmelden)"}}};var Mt={consentModal:{description:"Aqu\xED puedes evaluar y personalizar los servicios que nos gustar\xEDa utilizar en este sitio web. \xA1T\xFA decides! Habilita o deshabilita los servicios como mejor te parezca.",privacyPolicy:{text:"Para saber m\xE1s, por favor lee nuestra {privacyPolicy}."}},consentNotice:{changeDescription:"Ha habido cambios desde tu \xFAltima visita. Por favor renueva tu consentimiento.",description:"\xA1Hola! \xBFPodemos habilitar algunos servicios adicionales para {purposes}? Siempre puedes cambiar o retirar tu consentimiento m\xE1s tarde.",learnMore:"Quiero elegir"},contextualConsent:{description:"\xBFQuieres cargar el contenido externo suministrado por {title}?"},decline:"No, gracias",ok:"De acuerdo",privacyPolicy:{text:"Para saber m\xE1s, por favor lee nuestra {privacyPolicy}."},purposes:{advertising:{description:"Estos servicios procesan informaci\xF3n personal para mostrarte anuncios personalizados o basados en tus intereses."},functional:{description:"Estos servicios son esenciales para el correcto funcionamiento de este sitio web. No puedes desactivarlos ya que la p\xE1gina no funcionar\xEDa correctamente."},marketing:{description:"Estos servicios procesan informaci\xF3n personal para mostrarte contenido relevante sobre productos, servicios o temas que puedan interesarte."}},service:{disableAll:{description:"Usa este interruptor para activar o desactivar todos los servicios."},optOut:{description:"Este servicio est\xE1 habilitado por defecto (pero puedes optar por desactivarlo)"}}};var Rt={consentModal:{description:"Tu peux \xE9valuer et personnaliser ici les services que nous aimerions utiliser sur ce site. C'est toi qui d\xE9cides ! Active ou d\xE9sactive les services comme bon te semble.",privacyPolicy:{text:"Pour en savoir plus, lis notre {privacyPolicy}."}},consentNotice:{changeDescription:"Il y a eu des changements depuis ta derni\xE8re visite, renouvelle ton consentement s'il te pla\xEEt.",description:"Salut ! On peut activer quelques services suppl\xE9mentaires pour {purposes} ? Tu peux modifier ou retirer ton consentement \xE0 tout moment.",learnMore:"Laisse-moi choisir"},contextualConsent:{description:"Tu veux charger un contenu externe fourni par {title} ?"},decline:"Non merci",ok:"\xC7a marche",privacyPolicy:{text:"Pour en savoir plus, lis notre {privacyPolicy}."},purposes:{advertising:{description:"Ces services traitent les informations personnelles pour te pr\xE9senter des publicit\xE9s personnalis\xE9es ou bas\xE9es sur tes int\xE9r\xEAts."},marketing:{description:"Ces services traitent les informations personnelles afin de te pr\xE9senter du contenu pertinent sur les produits, les services ou les sujets qui pourraient t'int\xE9resser."}},service:{disableAll:{description:"Utilise ce bouton pour activer ou d\xE9sactiver tous les services."},optOut:{description:"Ce service est charg\xE9 par d\xE9faut (mais tu peux le d\xE9sactiver)"}}};var Dt={consentModal:{description:"Qui puoi valutare e personalizzare i servizi che vorremmo utilizzare su questo sito web. Sei tu a decidere! Abilita o disabilita i servizi come preferisci.",privacyPolicy:{text:"Per saperne di pi\xF9, leggi la nostra {privacyPolicy}."}},consentNotice:{changeDescription:"Ci sono stati dei cambiamenti dalla tua ultima visita, ti chiediamo di rinnovare il tuo consenso.",description:"Ciao! Possiamo attivare alcuni servizi aggiuntivi per {purposes}? Puoi sempre modificare o ritirare il tuo consenso in un secondo momento.",learnMore:"Fammi scegliere"},contextualConsent:{description:"Vuoi caricare contenuti esterni forniti da {title}?"},decline:"No, grazie",ok:"Va bene",privacyPolicy:{text:"Per saperne di pi\xF9, leggi la nostra {privacyPolicy}."},purposes:{advertising:{description:"Questi servizi elaborano le informazioni personali per mostrarti annunci pubblicitari personalizzati o basati sui tuoi interessi."},marketing:{description:"Questi servizi elaborano le informazioni personali per mostrarti contenuti rilevanti su prodotti, servizi o argomenti che potrebbero interessarti."}},service:{disableAll:{description:"Usa questo interruttore per attivare o disattivare tutti i servizi."},optOut:{description:"Questo servizio \xE8 caricato di default (ma puoi scegliere di non usarlo)"}}};var Pt={consentModal:{description:"Hier kun je de diensten beoordelen en aanpassen die wij op deze website willen gebruiken. Jij hebt de leiding! Schakel de diensten naar eigen inzicht in of uit.",privacyPolicy:{text:"Voor meer informatie kun je ons {privacyPolicy} lezen."}},consentNotice:{changeDescription:"Er waren veranderingen sinds je laatste bezoek, vernieuw je toestemming alsjeblieft.",description:"Hoi! Kunnen we een aantal extra diensten voor {purposes} inschakelen? Je kunt je toestemming later altijd nog wijzigen of intrekken.",learnMore:"Laat me kiezen"},contextualConsent:{description:"Wil je externe content laden die door {title} wordt aangeleverd?"},decline:"Nee, bedankt",ok:"Klinkt goed",privacyPolicy:{text:"Voor meer informatie kun je ons {privacyPolicy} lezen."},purposes:{advertising:{description:"Deze diensten verwerken persoonlijke informatie om je gepersonaliseerde of op interesse gebaseerde advertenties te tonen."},functional:{description:"Deze diensten zijn essentieel voor het correct functioneren van deze website. Je kunt ze hier niet uitschakelen omdat de dienst anders niet correct zou werken."},marketing:{description:"Deze diensten verwerken persoonlijke informatie om je relevante inhoud te tonen over producten, diensten of onderwerpen waarin je ge\xEFnteresseerd zou kunnen zijn."}},service:{disableAll:{description:"Gebruik deze schakelaar om alle diensten in of uit te schakelen."},optOut:{description:"Deze diensten worden standaard geladen (maar je kunt je afmelden)"}}};var ti={de:Tt,es:Mt,fr:Rt,it:Dt,nl:Pt},Ot=ti;function Lt(r){let e=new Set;for(let t of r.services){let n=t.purposes??[];for(let i of n)e.add(i)}return Array.from(e)}function ne(r,e,t=!0){let n=r;for(let i of Object.keys(e)){if(i==="__proto__"||i==="constructor"||i==="prototype")continue;let o=e[i],s=n[i];typeof o=="string"?(t||s===void 0)&&(n[i]=o):typeof o=="object"&&o!==null&&(typeof s=="object"&&s!==null?ne(s,o,t):(t||s===void 0)&&(n[i]=o))}return r}function ni(r,...e){let t=e[0],n;e.length===0?n={}:typeof t=="string"||typeof t=="number"?n=Array.prototype.slice.call(e):n=t??{};let i=[],o=String(r);for(;o.length>0;){let s=o.match(/\{(?!\{)([\w\d]+)\}(?!\})/);if(s===null||s.index===void 0||s[1]===void 0){i.push(o),o="";break}let a=o.substring(0,s.index);o=o.substring(s.index+s[0].length),i.push(a);let c=Number.parseInt(s[1],10);Number.isNaN(c)?i.push(n[s[1]]):i.push(n[c])}return i}function Ke(r){if(r?.lang!==void 0&&r.lang!=="zz")return r.lang;let e=typeof window<"u"?window:void 0,t=typeof document<"u"?document.documentElement.lang:void 0,n=r?.languages?.[0],i=((typeof e?.language=="string"?e.language:null)||t||n||"en").toLowerCase(),s=/^([\w]+)-([\w]+)$/.exec(i);return s===null||s[1]===void 0?i:s[1]}function Nt(r,e,t){let n=Array.isArray(e)?e:[e],i=r;for(let o of n){if(i===void 0)return t;if(typeof o=="string"&&o.endsWith("?")){let s=o.slice(0,-1),a=i instanceof Map?i.get(s):i[s];typeof a=="string"&&(i=a)}else if(i instanceof Map)i=i.get(o);else if(i!==null&&typeof i=="object")i=i[o];else return t}if(typeof i!="string")return t;if(i!=="")return i}function Ut(r,e,t,n,...i){let o=n,s=!1;o[0]==="!"&&(o=o.slice(1),s=!0),Array.isArray(o)||(o=[o]);let a=Nt(r,[e,...o]);return a===void 0&&t!==void 0&&(a=Nt(r,[t,...o])),a===void 0?s?void 0:[`[missing translation: ${e}/${o.join("/")}]`]:i.length>0?ni(a,...i):a}function W(r){let e=new Map;for(let t of Object.keys(r)){let n=r[t];typeof n=="string"||n===null?e.set(t,n):typeof n=="object"&&n!==null&&e.set(t,W(n))}return e}function E(r,e,t=!0,n=!1){if(!(e instanceof Map)||!(r instanceof Map))throw new Error("Parameters are not maps!");let i=n?new Map(r):r,o=(s,a,c)=>{if(c instanceof Map){let l=new Map;E(l,c,!0,!1),s.set(a,l)}else s.set(a,c)};for(let s of e.keys()){let a=e.get(s),c=i.get(s);i.has(s)?a instanceof Map&&c instanceof Map?i.set(s,E(c,a,t,n)):t&&o(i,s,a):o(i,s,a)}return i}var ii={analytics:["analytics_storage"],marketing:["ad_storage","ad_user_data","ad_personalization"]},ri=["marketing"],oi="simplecmp_consent_update",si=500,ai=["google"];function ci(r){let e=typeof r=="object"&&r!==null?r:{},t=e.dataLayerEvent;return{vendors:e.vendors??ai,purposeSignals:e.purposeSignals??ii,adPurposes:e.adPurposes??ri,waitForUpdate:e.waitForUpdate??si,dataLayerEvent:t===!1?null:typeof t=="string"?t:oi,redactAdsData:e.redactAdsData??!1}}function li(r){return Array.isArray(r.dataLayer)||(r.dataLayer=[]),typeof r.gtag!="function"&&(r.gtag=function(){r.dataLayer.push(arguments)}),r.gtag}function di(r){let e=new Set;for(let t of Object.values(r))for(let n of t)e.add(n);return[...e]}function pi(r,e){return Object.keys(e).filter(t=>e[t]?.includes(r))}function ui(r,e){let t=li(e),n=di(r.purposeSignals),i=o=>{let s={};for(let a of n){let c=pi(a,r.purposeSignals).some(l=>o.granted(l));s[a]=c?"granted":"denied"}return s};return{id:"google",emit(o,s){let a=i(s);r.redactAdsData&&n.includes("ad_storage")&&t("set","ads_data_redaction",a.ad_storage!=="granted"),o==="default"?t("consent","default",{...a,security_storage:"granted",wait_for_update:r.waitForUpdate}):(t("consent","update",{...a}),r.dataLayerEvent!==null&&Array.isArray(e.dataLayer)&&e.dataLayer.push({event:r.dataLayerEvent}))}}}function fi(r,e){return{id:"meta",emit(t,n){if(typeof e.fbq!="function")return;let i=r.adPurposes.some(o=>n.granted(o));e.fbq("consent",i?"grant":"revoke")}}}function mi(r,e){return{id:"microsoftUet",emit(t,n){Array.isArray(e.uetq)||(e.uetq=[]);let i=r.adPurposes.some(o=>n.granted(o))?"granted":"denied";e.uetq.push("consent",t,{ad_storage:i})}}}function hi(r,e){let t=[];for(let n of r.vendors)n==="google"?t.push(ui(r,e)):n==="meta"?t.push(fi(r,e)):n==="microsoftUet"&&t.push(mi(r,e));return t}function Je(r,e,t){if(typeof window>"u")return()=>{};let n=ci(r),i=hi(n,window);if(i.length===0)return()=>{};let o=c=>({granted:l=>t.some(f=>(f.purposes??[]).includes(l)&&c(f))}),s=(c,l)=>{let f=o(l);for(let p of i)p.emit(c,f)};s("default",c=>e.getDefaultConsent(c)),e.confirmed&&s("update",c=>e.getConsent(c.name));let a={update:(c,l)=>{l!=="consents"&&l!=="saveConsents"||s("update",f=>c.getConsent(f.name))}};return e.watch(a),()=>e.unwatch(a)}var gi,Qe=new Map,ie={},It={},we={},Se={};function Bt(r,e){we[r]===void 0?we[r]=[e]:we[r].push(e);let t=Se[r];if(t!==void 0){for(let n of t)if(e(...n)===!1)break}}function Xe(r,...e){let t=we[r];if(Se[r]===void 0?Se[r]=[e]:Se[r].push(e),t!==void 0){for(let n of t)if(n(...e)===!0)return!0}}function vi(r){let e={...r};if(e.version===2)return e;if(e.apps!==void 0&&e.services===void 0&&(e.services=e.apps,console.warn("Warning, your configuration file is outdated. Please change `apps` to `services`"),delete e.apps),e.translations!==void 0&&typeof e.translations=="object"&&e.translations!==null){let t=e.translations;t.apps!==void 0&&t.services===void 0&&(t.services=t.apps,console.warn("Warning, your configuration file is outdated. Please change `apps` to `services` in the `translations` key"),delete t.apps)}return e}function jt(r){let e=new Map;E(e,Qe),yi(e,r);let t=r.translations??{};return E(e,W(t)),e}function yi(r,e){let t=e.tones;if(t!=null)for(let[n,i]of Object.entries(t)){if(i!=="informal")continue;let o=Ot[n];if(o===void 0)continue;let s=new Map;s.set(n,W(o)),E(r,s)}}function re(r){let e=r??gi;if(!e)throw new Error("SimpleCMP getManager called without config and no default config set");let t=e.storageName??e.cookieName??"default",n=bi(e);return(ie[t]===void 0||It[t]!==n)&&(ie[t]=new te(vi(e)),It[t]=n,ie[t].versionMismatch!==void 0&&Xe("consentVersionMismatch",ie[t].versionMismatch)),ie[t]}function bi(r){try{return JSON.stringify(r,(e,t)=>typeof t=="function"?void 0:t)}catch{return`__unserialisable__:${ki++}`}}var ki=0;var Ht={acceptAll:"Accept all",acceptSelected:"Accept selected",close:"Close",consentModal:{description:"Here you can assess and customize the services that we'd like to use on this website. You're in charge! Enable or disable services as you see fit.",title:"Services we would like to use"},consentNotice:{changeDescription:"There were changes since your last visit, please renew your consent.",title:"Cookie Consent",description:"Hi! Could we please enable some additional services for {purposes}? You can always change or withdraw your consent later.",optOutDescription:"We use cookies and similar technologies, including for {purposes}. They are on by default \u2014 you can opt out at any time.",imprint:{name:"Imprint"},learnMore:"Let me choose",testing:"Testing mode!"},doNotSell:"Do Not Sell or Share My Personal Information",contextualConsent:{acceptAlways:"Always",acceptOnce:"Yes",description:"Do you want to load external content supplied by {title}?",descriptionEmptyStore:"To agree to this service permanently, you must accept {title} in the {link}.",descriptionUnknownHost:"Blocked third-party content from {title}. The site administrator has not reviewed this source \u2014 please contact them to enable it.",modalLinkText:"Consent Manager",providerInfoLink:"More information \u203A"},providerInfo:{title:"Provider information",close:"Close",noData:"No provider information available.",field:{vendor:"Provider",description:"Description",address:"Address",country:"Country",privacyPolicy:"Privacy policy",optOut:"Opt-out",partner:"Partners / joint controllers"}},decline:"I decline",ok:"That's ok",poweredBy:"Realized with Klaro!",privacyPolicy:{name:"privacy policy",text:"To learn more, please read our {privacyPolicy}."},purposeItem:{service:"service",services:"services"},purposes:{advertising:{description:"These services process personal information to show you personalized or interest-based advertisements.",title:"Advertising"},analytics:{description:"These services collect data about how visitors use this site so we can measure and improve its performance.",title:"Analytics"},functional:{description:`These services are essential for the correct functioning of this website. You cannot disable them here as the service would not work correctly otherwise.
`,title:"Service Provision"},marketing:{description:"These services process personal information to show you relevant content about products, services or topics that you might be interested in.",title:"Marketing"},performance:{description:`These services process personal information to optimize the service that this website offers.
`,title:"Performance Optimization"},personalization:{description:"These services tailor what you see on this site to your preferences and prior interactions.",title:"Personalization"},security:{description:"These services protect this site against abuse \u2014 for example, by detecting suspicious traffic or blocking automated attacks.",title:"Security"}},save:"Save",service:{disableAll:{description:"Use this switch to enable or disable all services.",title:"Enable or disable all services"},optOut:{description:"This services is loaded by default (but you can opt out)",title:"(opt-out)"},purpose:"purpose",purposes:"purposes",required:{description:"This services is always required",title:"(always required)"}}};var zt="simplecmp-reported:";function qt(r){let e=r.indexOf("?"),t=r.indexOf("#"),n=[e,t].filter(i=>i>=0);return n.length===0?r:r.slice(0,Math.min(...n))}var U=class{constructor(e){this.refreshInFlight=null;this.lastSent=new Map;this.warned=new Set;this.pending=[];this.pendingKeys=[];this.flushTimer=null;this.lifecycleHooked=!1;this.url=e.url,this.host=(()=>{try{return new URL(e.url).hostname}catch{return""}})(),this.auth=e.auth?{...e.auth}:void 0,this.source=e.source??"default",this.dedupTtlMs=e.dedupTtlMs??36e5,this.crossSessionDedupMs=e.crossSessionDedupMs??6048e5,this.reportGeneration=Math.max(0,Math.floor(e.reportGeneration??0)),this.flushDebounceMs=e.flushDebounceMs??1500,this.maxBatchSize=Math.max(1,e.maxBatchSize??25),this.timeoutMs=e.timeoutMs??5e3,this.respectDoNotTrack=e.respectDoNotTrack??!0,this.fetchFn=e.fetch??(typeof fetch<"u"?fetch.bind(globalThis):void 0),this.now=e.now??(()=>Date.now()),e.storage!==void 0?this.storage=e.storage:typeof localStorage<"u"?this.storage=localStorage:this.storage=null,this.nav=e.navigator??(typeof navigator<"u"?navigator:void 0);let t=e.sampleRate??1;this.sessionInScope=t>=1||Math.random()<t}onDetection(e){if(!this.sessionInScope||this.respectDoNotTrack&&this.nav?.doNotTrack==="1"||this.host&&e.origin===this.host)return;let t=`${e.kind}:${e.identifier}`;if(this._dedupHit(t))return;let n=this.now();if(this.lastSent.set(t,n),this.pendingKeys.push(t),this.pending.push(this._toBridgeDetection(e)),this.pending.length>=this.maxBatchSize){this._flush();return}this._scheduleFlush(),this._hookLifecycle()}flushNow(){return this._flush()}_dedupHit(e){let t=this.now(),n=this.lastSent.get(e);return n!==void 0&&t-n<this.dedupTtlMs?!0:this._crossSessionHit(e,t)}_crossSessionHit(e,t){if(this.storage===null||this.crossSessionDedupMs<=0)return!1;let n=`${zt}${this.source}:${e}`,i=null;try{i=this.storage.getItem(n)}catch{return!1}if(i===null)return!1;let o=this._parseMarker(i);if(o===null)return!1;if(o.gen<this.reportGeneration){try{this.storage.removeItem(n)}catch{}return!1}if(t-o.ts<this.crossSessionDedupMs)return!0;try{this.storage.removeItem(n)}catch{}return!1}_markCrossSession(e,t){if(this.storage===null||this.crossSessionDedupMs<=0)return;let n=`${zt}${this.source}:${e}`;try{this.storage.setItem(n,`${this.reportGeneration}.${t}`)}catch{}}_parseMarker(e){let t=e.indexOf(".");if(t===-1){let o=Number(e);return Number.isFinite(o)?{gen:0,ts:o}:null}let n=Number(e.slice(0,t)),i=Number(e.slice(t+1));return!Number.isFinite(n)||!Number.isFinite(i)?null:{gen:n,ts:i}}_scheduleFlush(){this.flushTimer!==null||typeof setTimeout>"u"||(this.flushTimer=setTimeout(()=>{this.flushTimer=null,this._flush()},this.flushDebounceMs))}_hookLifecycle(){if(this.lifecycleHooked||typeof addEventListener>"u")return;this.lifecycleHooked=!0;let e=()=>{this._flushBeacon()};addEventListener("pagehide",e,{capture:!0}),typeof document<"u"&&document.addEventListener("visibilitychange",()=>{document.visibilityState==="hidden"&&e()},{capture:!0})}_flushBeacon(){if(this.pending.length===0)return;if(typeof this.nav?.sendBeacon!="function"){this._flush({keepalive:!0});return}let e=this._buildPayload(this.pending),t=new Blob([JSON.stringify(e)],{type:"application/json"}),n=!1;try{n=this.nav.sendBeacon(this.url,t)}catch{n=!1}n?(this._markBatchSent(),this.pending=[],this.pendingKeys=[]):this._flush({keepalive:!0})}async _flush(e={}){if(this.flushTimer!==null&&(clearTimeout(this.flushTimer),this.flushTimer=null),this.pending.length===0)return;let t=this.pending,n=this.pendingKeys;this.pending=[],this.pendingKeys=[];try{await this._post(this._buildPayload(t),e);let i=this.now();for(let o of n)this._markCrossSession(o,i)}catch(i){if(this._shouldClearOnError(i))for(let o of n)this.lastSent.delete(o);this._warnOnce("post",i)}}_markBatchSent(){let e=this.now();for(let t of this.pendingKeys)this._markCrossSession(t,e)}_toBridgeDetection(e){let t={kind:e.kind,identifier:e.identifier,firstSeen:e.firstSeen,lastSeen:e.lastSeen,count:e.count,status:e.status==="known"?"known":"unknown"};return e.origin!==void 0&&(t.origin=e.origin),e.firstSeenOn!==void 0&&(t.firstSeenOn=qt(e.firstSeenOn)),e.matchedService!==void 0&&(t.matchedService=e.matchedService),t}_buildPayload(e){let t=typeof location<"u"?location:void 0,n=typeof document<"u"?document:void 0,i=typeof navigator<"u"?navigator:void 0,o={url:t?qt(t.href):""},s=n?.referrer;s&&(o.referrer=s);let a=i?.userAgent;return a&&(o.userAgent=a),{schemaVersion:2,source:this.source,sentAt:new Date(this.now()).toISOString(),page:o,library:{name:"simplecmp",version:"0.4.1"},detections:e}}async _post(e,t){if(!this.fetchFn)throw new Error("fetch is unavailable");let n=new Headers({"Content-Type":"application/json"});if(this.auth){let s=this.auth.header??"Authorization",a=this.auth.scheme??"Bearer";n.set(s,`${a} ${this.auth.token}`.trim())}let i=typeof AbortController<"u"?new AbortController:null,o=i&&typeof setTimeout<"u"?setTimeout(()=>i.abort(),this.timeoutMs):void 0;try{let s={method:"POST",headers:n,body:JSON.stringify(e),signal:i?.signal};t.keepalive===!0&&(s.keepalive=!0);let a=await this.fetchFn(this.url,s);if(a.status===401&&!t.retried&&this.auth?.refreshUrl){let c=await this._refreshToken();if(c!==null&&this.auth)return this.auth.token=c,this._post(e,{...t,retried:!0});throw new Error("CMS bridge POST responded 401")}if(!a.ok)throw new Error(`CMS bridge POST responded ${a.status}`)}finally{o!==void 0&&clearTimeout(o)}}async _refreshToken(){let e=this.auth?.refreshUrl;if(!e||!this.fetchFn)return null;if(this.refreshInFlight!==null)try{return await this.refreshInFlight}catch{return null}let t=typeof AbortController<"u"?new AbortController:null,n=Math.min(2e3,this.timeoutMs),i=t&&typeof setTimeout<"u"?setTimeout(()=>t.abort(),n):void 0,o=this.fetchFn;this.refreshInFlight=(async()=>{try{let s=await o(e,{method:"GET",signal:t?.signal});if(!s.ok)throw new Error(`refresh responded ${s.status}`);let a=await s.json();if(typeof a?.token!="string"||a.token==="")throw new Error("refresh response missing token");return a.token}finally{i!==void 0&&clearTimeout(i)}})();try{return await this.refreshInFlight}catch(s){return this._warnOnce("tokenRefresh",s),null}finally{this.refreshInFlight=null}}_shouldClearOnError(e){let t=e instanceof Error?e.message:String(e);return/responded 4\d\d/.exec(t)===null}_warnOnce(e,t){if(this.warned.has(e))return;this.warned.add(e);let n=t instanceof Error?t.message:String(t);console.warn(`SimpleCMP cms-bridge: ${e} failed (${n}). The bridge will keep trying on subsequent detection events; this warning fires once per error category per session.`)}};var Ft="simplecmp.servicedb.";function Ye(r,e){let t=e.cookie?`c:${e.cookie}`:`o:${e.origin??""}`;return`${Ft}${r}.${t}`}function Si(r,e){let t=r.get("Cache-Control");if(!t)return e;let n=/max-age=(\d+)/.exec(t);if(!n||!n[1])return e;let i=Number.parseInt(n[1],10);return Number.isNaN(i)||i<0?e:i*1e3}var V=class{constructor(e){this.inflight=new Map;this.warned=new Set;this.url=e.url.replace(/\/+$/,""),this.host=(()=>{try{return new URL(this.url).host}catch{return this.url}})(),this.auth=e.auth,this.cacheTtlMs=e.cacheTtlMs??864e5,this.timeoutMs=e.timeoutMs??3e3,this.apiVersion=e.apiVersion??"v1",this.fetchFn=e.fetch??(typeof fetch<"u"?fetch.bind(globalThis):void 0),this.storage=e.storage??(typeof localStorage<"u"?localStorage:void 0),this.now=e.now??(()=>Date.now())}async lookup(e){let t=Ye(this.host,e),n=this._readCache(t);return n!==void 0?(this.now()-n.storedAt<n.maxAgeMs||this._revalidate(t,e),n.match):this._fetchAndCache(t,e)}async lookupBatch(e){if(e.length===0)return[];let t=new Array(e.length).fill(void 0),n=[];for(let i=0;i<e.length;i++){let o=e[i];if(!o)continue;let s=Ye(this.host,o),a=this._readCache(s);a!==void 0&&this.now()-a.storedAt<a.maxAgeMs?t[i]=a.match:n.push({index:i,query:o})}if(n.length===0)return t.map(i=>i??null);try{let i=JSON.stringify({items:n.map(a=>a.query)}),s=(await this._request(`/${this.apiVersion}/lookup`,{method:"POST",headers:{"Content-Type":"application/json"},body:i}))?.items??[];for(let a=0;a<n.length;a++){let c=n[a],l=s[a];if(!c)continue;let f=l?.matches?.[0]??null,p=Ye(this.host,c.query);this._writeCache(p,f,this.cacheTtlMs),t[c.index]=f}}catch(i){this._warnOnce("batch-lookup",i);for(let o of n)t[o.index]=null}return t.map(i=>i??null)}clearCache(){if(!this.storage)return;let e=this.storage;if(typeof e.length!="number"||typeof e.key!="function")return;let t=`${Ft}${this.host}.`,n=[];for(let i=0;i<e.length;i++){let o=e.key(i);o?.startsWith(t)&&n.push(o)}for(let i of n)this.storage.removeItem(i)}async health(){try{return await this._request(`/${this.apiVersion}/health`,{method:"GET"})}catch{return null}}async _fetchAndCache(e,t){let n=this.inflight.get(e);if(n)return n;let i=(async()=>{try{let s=(await this._request(`/${this.apiVersion}/lookup`,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({items:[t]})}))?.items?.[0]?.matches?.[0]??null;return this._writeCache(e,s,this.cacheTtlMs),s}catch(o){return this._warnOnce("lookup",o),null}finally{this.inflight.delete(e)}})();return this.inflight.set(e,i),i}async _revalidate(e,t){this.inflight.has(e)||await this._fetchAndCache(e,t).catch(()=>{})}async _request(e,t){if(!this.fetchFn)throw new Error("fetch is unavailable");let n=new Headers(t.headers);if(this.auth){let s=this.auth.header??"Authorization",a=this.auth.scheme??"Bearer";n.set(s,`${a} ${this.auth.token}`.trim())}let i=typeof AbortController<"u"?new AbortController:null,o=i&&typeof setTimeout<"u"?setTimeout(()=>i.abort(),this.timeoutMs):void 0;try{let s=await this.fetchFn(`${this.url}${e}`,{...t,headers:n,signal:i?.signal});if(!s.ok)throw new Error(`Service DB ${e} responded ${s.status}`);return await s.json()}finally{o!==void 0&&clearTimeout(o)}}_readCache(e){if(this.storage)try{let t=this.storage.getItem(e);if(!t)return;let n=JSON.parse(t);return typeof n.storedAt!="number"||typeof n.maxAgeMs!="number"?void 0:n}catch{return}}_writeCache(e,t,n){if(this.storage)try{let i={match:t,storedAt:this.now(),maxAgeMs:n};this.storage.setItem(e,JSON.stringify(i))}catch{}}_absorbCacheControl(e){return Si(e,this.cacheTtlMs)}_warnOnce(e,t){if(this.warned.has(e))return;this.warned.add(e);let n=t instanceof Error?t.message:String(t);console.warn(`SimpleCMP service-db: ${e} failed (${n}). Falling back to local classification for this session category.`)}};function _e(r,e,t){if(typeof e=="string"){if(e.length>=2&&e.startsWith("/")&&e.endsWith("/"))try{return new RegExp(e.slice(1,-1)).test(r)}catch{return!1}return e===r}if(e instanceof RegExp)return e.test(r);if(Array.isArray(e)&&e.length>0){let n=e[0];if(typeof n!="string")return!1;try{return new RegExp(n).test(r)}catch{return n===r}}return typeof e=="object"&&e!==null&&"name"in e&&"requireOrigin"in e&&_i(e.requireOrigin,t)?_e(r,e.name,t):!1}function Ce(r,e){if(e instanceof RegExp)return e.test(r);if(typeof e!="string")return!1;if(e.length>=2&&e.startsWith("/")&&e.endsWith("/"))try{return new RegExp(`^(?:${e.slice(1,-1)})$`).test(r)}catch{return!1}if(e.startsWith("*.")){let t=e.slice(2);return r===t||r.endsWith(`.${t}`)}return e===r}function _i(r,e){for(let t of e)if(Ce(t,r))return!0;return!1}var G=class{constructor(e){this.services=e;this.observedOrigins=new Set}classify(e){e.kind!=="cookie"&&e.origin&&this.observedOrigins.add(e.origin);let t=this._findService(e);return t?{matchedService:t,status:"known"}:{status:"unknown"}}hasObservedOrigin(e){return this.observedOrigins.has(e)}get observedOriginsView(){return this.observedOrigins}_findService(e){if(e.kind==="cookie"){for(let t of this.services)if(t.cookies){for(let n of t.cookies)if(_e(e.identifier,n,this.observedOrigins))return t.name}return}if(e.origin){for(let t of this.services)if(t.origins){for(let n of t.origins)if(Ce(e.origin,n))return t.name}}}};var K=class{constructor(e,t){this.dbClient=e;this.listeners=new Set;this.local=new G(t)}classify(e){let t=this.local.classify(e);if(t.status==="known")return t;let n=e.kind==="cookie"?{cookie:e.identifier}:e.origin?{origin:e.origin}:null;if(!n)return t;let i=this.dbClient.lookup(n).then(o=>{o&&(e.kind==="cookie"&&!this._hostQualifierPasses(o,e.identifier)||this._dispatch(e,this._toEnrichment(o)))}).catch(()=>{});return{...t,pending:i}}_hostQualifierPasses(e,t){let n=e.matches?.cookies??[];if(n.length===0)return!0;let i=this.local.observedOriginsView;for(let o of n)if(_e(t,o,i))return!0;return!1}onEnrichment(e){this.listeners.add(e)}offEnrichment(e){this.listeners.delete(e)}_dispatch(e,t){for(let n of this.listeners)try{n(e,t)}catch(i){console.warn("SimpleCMP service-db: enrichment listener threw:",i)}}_toEnrichment(e){return{matchedService:e.id,matchedVendor:e.vendor,status:"known"}}};var Ci="simplecmp.recorder.";function Wt(r){return!!(!r||r==="localhost"||r.endsWith(".localhost")||r.endsWith(".local")||r.endsWith(".test")||/^127\.\d+\.\d+\.\d+$/.test(r)||/^192\.168\.\d+\.\d+$/.test(r)||/^10\.\d+\.\d+\.\d+$/.test(r)||r==="::1"||r==="0.0.0.0")}var xe=class{constructor(e){this.listeners=new Set;this.settledListeners=new Set;this.detections=new Map;this.active=!1;this.options=e.options,this.classifier=e.classifier,this.services=e.services,this.onDetectionForLibEvent=e.onDetectionForLibEvent;let t=n=>this._ingest(n);this.watchers=e.watcherFactories.map(n=>n(t))}start(){if(this.active)return;this.active=!0;let e=typeof location<"u"?location.hostname:"",t=Wt(e);!t&&!this.options.silenceProductionWarning&&console.warn(`SimpleCMP: recorder is active on a hostname that looks like production (${e||"unknown"}). Set \`record: { silenceProductionWarning: true }\` to suppress this warning if intentional.`),this.options.persistInDev&&t&&this._loadFromStorage();for(let i of this.watchers)i.start();let n=this.options.summaryIntervalMs??3e4;n>0&&typeof setInterval<"u"&&(this.summaryTimer=setInterval(()=>this._logSummary(),n))}stop(){if(this.active){this.active=!1;for(let e of this.watchers)e.stop();this.summaryTimer!==void 0&&(clearInterval(this.summaryTimer),this.summaryTimer=void 0)}}getSnapshot(){return Array.from(this.detections.values())}clear(){this.detections.clear(),this._writeToStorage()}on(e,t){e==="detection"?this.listeners.add(t):e==="detectionSettled"&&this.settledListeners.add(t)}off(e,t){e==="detection"?this.listeners.delete(t):e==="detectionSettled"&&this.settledListeners.delete(t)}recordSyntheticDetection(e){this.active&&this._ingest(e)}enrichDetection(e,t){let n=`${e.kind}:${e.identifier}`,i=this.detections.get(n);if(!i)return;let o={...i,...t,lastSeen:Date.now()};this.detections.set(n,o),this._announce(o),this._writeToStorage()}exportConfig(){let e=new Map;for(let n of this.services){let i={name:n.name};n.cookies&&(i.cookies=n.cookies.slice()),n.origins&&(i.origins=n.origins.slice()),e.set(n.name,i)}let t=1;for(let n of this.detections.values()){if(n.status!=="unknown")continue;let i=n.kind==="cookie"?`unknown-cookie-${t++}`:`unknown-${n.origin?.replace(/[^a-z0-9]+/gi,"-")??"origin"}-${t++}`,o={name:i,purposes:[]};n.kind==="cookie"?o.cookies=[n.identifier]:n.origin&&(o.origins=[n.origin]),e.set(i,o)}return{services:Array.from(e.values())}}assertNoUnknown(){let e=this.getSnapshot().filter(n=>n.status==="unknown");if(e.length===0)return;let t=e.map(n=>`  - [${n.kind}] ${n.identifier}${n.origin?` (${n.origin})`:""}`).join(`
`);throw new Error(`SimpleCMP recorder: ${e.length} unknown detection(s):
${t}
Add a service for each, or pass \`record: { silenceProductionWarning: true }\` if intentional.`)}_ingest(e){if(e.kind==="cookie"&&this.options.ignoreCookies?.includes(e.identifier))return;let t=`${e.kind}:${e.identifier}`,n=Date.now(),i=this.detections.get(t);if(i){i.lastSeen=n,i.count+=1;return}let o=this.classifier.classify(e),{pending:s,...a}=o,c={kind:e.kind,identifier:e.identifier,origin:e.origin,firstSeen:n,lastSeen:n,firstSeenOn:e.firstSeenOn,count:1,...a};this.detections.set(t,c),this._announce(c),this._writeToStorage(),e.kind!=="cookie"&&e.origin&&this._reclassifyUnknownCookiesOnNewOrigin(),s?s.finally(()=>this._announceSettled(t)):this._announceSettled(t)}_announce(e){typeof console<"u"&&typeof console.info=="function"&&console.info(`[SimpleCMP recorder] ${e.kind} ${e.status==="unknown"?"\u{1F7E1} unknown":`\u2192 ${e.matchedService}`}: ${e.identifier}`);for(let t of[...this.listeners])try{t(e)}catch(n){console.warn("SimpleCMP recorder: listener threw:",n)}if(this.onDetectionForLibEvent)try{this.onDetectionForLibEvent(e)}catch(t){console.warn("SimpleCMP recorder: lib-event handler threw:",t)}}_reclassifyUnknownCookiesOnNewOrigin(){for(let e of this.detections.values()){if(e.kind!=="cookie"||e.status!=="unknown")continue;let t=this.classifier.classify({kind:e.kind,identifier:e.identifier,origin:e.origin,firstSeenOn:e.firstSeenOn});if(t.status==="known"&&t.matchedService){let n={status:"known",matchedService:t.matchedService};t.matchedVendor!==void 0&&(n.matchedVendor=t.matchedVendor),this.enrichDetection({kind:e.kind,identifier:e.identifier},n)}}}_announceSettled(e){let t=this.detections.get(e);if(t)for(let n of[...this.settledListeners])try{n(t)}catch(i){console.warn("SimpleCMP recorder: settled listener threw:",i)}}_logSummary(){if(this.detections.size===0)return;let e=Array.from(this.detections.values()).map(t=>({kind:t.kind,identifier:t.identifier,origin:t.origin??"",status:t.status,service:t.matchedService??"",count:t.count,firstSeenOn:t.firstSeenOn??""}));typeof console.table=="function"&&(console.groupCollapsed("[SimpleCMP recorder] catalog"),console.table(e),console.groupEnd())}_storageKey(){return Ci+(this.options.storageName??"default")}_loadFromStorage(){if(!(typeof sessionStorage>"u"))try{let e=sessionStorage.getItem(this._storageKey());if(!e)return;let t=JSON.parse(e);if(t.schema!==1||!Array.isArray(t.detections))return;for(let n of t.detections)this.detections.set(`${n.kind}:${n.identifier}`,n)}catch{}}_writeToStorage(){if(this.options.persistInDev&&!(typeof location>"u"||!Wt(location.hostname))&&!(typeof sessionStorage>"u"))try{let e=JSON.stringify({schema:1,detections:Array.from(this.detections.values())});sessionStorage.setItem(this._storageKey(),e)}catch{}}};function xi(r){let e=new Set;if(!r)return e;for(let t of r.split(";")){let n=t.indexOf("="),i=(n>=0?t.slice(0,n):t).trim();i&&e.add(i)}return e}var Ae=class{constructor(e,t={}){this.seen=new Set;this.sink=e,this.intervalMs=t.intervalMs??1e3,this.readCookies=t.readCookies??(()=>typeof document<"u"?document.cookie:""),this.getPathname=()=>typeof location<"u"?location.pathname+location.search:void 0}start(){this.timerId===void 0&&(this._scan(),this.timerId=setInterval(()=>this._scan(),this.intervalMs))}stop(){this.timerId!==void 0&&(clearInterval(this.timerId),this.timerId=void 0)}scanOnce(){this._scan()}_scan(){let e=xi(this.readCookies()),t=this.getPathname();for(let n of e)this.seen.has(n)||(this.seen.add(n),this.sink({kind:"cookie",identifier:n,firstSeenOn:t}))}};var Ze={SCRIPT:"script",IFRAME:"iframe",IMG:"image",LINK:"link",AUDIO:"request",VIDEO:"request",SOURCE:"request",TRACK:"request",EMBED:"request",OBJECT:"request"};function Ai(r){let e=r.tagName;if(e==="LINK")return r.href||void 0;if(e==="OBJECT")return r.data||void 0;let t=r.getAttribute("src");if(t)try{return new URL(t,location.href).href}catch{return}}function $i(r){try{return new URL(r).hostname}catch{return}}var $e=class{constructor(e,t={}){this.seen=new Set;this.sink=e,this.root=t.root??(typeof document<"u"?document.documentElement:null)}start(){!this.root||this.observer||(this._initialScan(),!(typeof MutationObserver>"u")&&(this.observer=new MutationObserver(e=>this._onMutations(e)),this.observer.observe(this.root,{childList:!0,subtree:!0})))}stop(){this.observer&&(this.observer.disconnect(),this.observer=void 0)}_initialScan(){if(!this.root)return;let e=Object.keys(Ze).join(","),t=this.root.querySelectorAll(e);for(let n of Array.from(t))this._handleElement(n)}_onMutations(e){for(let t of e)for(let n of Array.from(t.addedNodes)){if(n.nodeType!==1)continue;let i=n;if(this._handleElement(i),i.querySelectorAll){let o=Object.keys(Ze).join(",");for(let s of Array.from(i.querySelectorAll(o)))this._handleElement(s)}}}_handleElement(e){let t=Ze[e.tagName];if(!t)return;let n=Ai(e);if(!n)return;let i=$i(n);if(!i||typeof location<"u"&&i===location.hostname)return;let o=`${t}:${n}`;if(this.seen.has(o))return;this.seen.add(o);let s={kind:t,identifier:n,origin:i,firstSeenOn:typeof location<"u"?location.pathname+location.search:void 0};this.sink(s)}};function Ei(r){try{return new URL(r).hostname}catch{return}}var Ee=class{constructor(e,t={}){this.seen=new Set;this.sink=e,this.perf=t.performance??(typeof performance<"u"?performance:void 0),this.Observer=t.PerformanceObserver??(typeof PerformanceObserver<"u"?PerformanceObserver:void 0)}start(){if(!this.observer&&(this._drainExisting(),!!this.Observer))try{this.observer=new this.Observer(e=>this._handleList(e)),this.observer.observe({type:"resource",buffered:!1})}catch{try{this.observer=new this.Observer(e=>this._handleList(e)),this.observer.observe({entryTypes:["resource"]})}catch{this.observer=void 0}}}stop(){this.observer&&(this.observer.disconnect(),this.observer=void 0)}_drainExisting(){if(this.perf)try{let e=this.perf.getEntriesByType("resource");for(let t of e)this._handleEntry(t)}catch{}}_handleList(e){for(let t of e.getEntries())this._handleEntry(t)}_handleEntry(e){let t=e.name;if(!t)return;let n=Ei(t);if(!n||typeof location<"u"&&n===location.hostname)return;let i=`request:${t}`;if(this.seen.has(i))return;this.seen.add(i);let o={kind:"request",identifier:t,origin:n,firstSeenOn:typeof location<"u"?location.pathname+location.search:void 0};this.sink(o)}};function Ti(){if(typeof window>"u"||typeof URLSearchParams>"u")return!1;try{return new URLSearchParams(window.location.search).get("simplecmp_discover")==="1"}catch{return!1}}function Vt(r){let e=typeof r.record=="object"&&r.record!==null?{...r.record}:{};if(!e.storageName&&typeof r.storageName=="string"&&(e.storageName=r.storageName),e.storageName){let s=e.ignoreCookies??[];e.ignoreCookies=s.includes(e.storageName)?s:[e.storageName,...s]}let t=r.services??[],n=r.serviceDbUrl?new K(new V({url:r.serviceDbUrl,auth:r.serviceDbAuth}),t):null,i=n??new G(t),o=new xe({options:e,classifier:i,services:t,watcherFactories:[s=>new Ae(s,{intervalMs:e.cookieIntervalMs}),s=>new $e(s),s=>new Ee(s)],onDetectionForLibEvent:s=>{Xe("recorderDetection",s)}});if(n&&n.onEnrichment((s,a)=>{o.enrichDetection(s,a)}),r.cmsBridgeUrl){let s=Ti(),a=new U({url:r.cmsBridgeUrl,auth:r.cmsBridgeAuth,source:r.cmsBridge?.source??e.storageName??"default",dedupTtlMs:r.cmsBridge?.dedupTtlMs,crossSessionDedupMs:s?0:r.cmsBridge?.crossSessionDedupMs,flushDebounceMs:r.cmsBridge?.flushDebounceMs,maxBatchSize:r.cmsBridge?.maxBatchSize,sampleRate:s?1:r.cmsBridge?.sampleRate,respectDoNotTrack:s?!1:r.cmsBridge?.respectDoNotTrack,timeoutMs:r.cmsBridge?.timeoutMs,reportGeneration:r.cmsBridge?.reportGeneration});o.on("detectionSettled",c=>a.onDetection(c))}return o}function Gt(r){switch(r){case"script-src":return"script";case"iframe-src":return"iframe";case"img-src":return"image";case"fetch":case"xhr":case"sendBeacon":return"request"}}function Kt(r){try{return new URL(r,window.location.href).hostname||void 0}catch{return}}function Jt(r){let e={matcher:r.matcher,consentChecker:r.consentChecker,sameOriginHosts:[window.location.host,...r.sameOriginHosts??[]],onBlock:r.onBlock??(()=>{})},t=[et(HTMLScriptElement.prototype,"script-src",e),et(HTMLIFrameElement.prototype,"iframe-src",e),et(HTMLImageElement.prototype,"img-src",e),Mi(e),Ri(e),Di(e)];return()=>{for(let n of t)n()}}function Te(r,e){if(!r||r==="about:blank")return null;let t;try{t=new URL(r,window.location.href)}catch{return null}let{host:n,hostname:i}=t;if(n===""||e.sameOriginHosts.includes(n))return null;let o=e.matcher(i);return o===null||e.consentChecker(o)?null:o}function et(r,e,t){let n=Object.getOwnPropertyDescriptor(r,"src");if(!n?.get||!n?.set)return()=>{};let i=n.set;return Object.defineProperty(r,"src",{configurable:!0,enumerable:n.enumerable,get:n.get,set(o){let s=this.getAttribute?.("data-name");if(s!=null&&t.consentChecker(s)===!0){i.call(this,o);return}let a=Te(o,t);if(a!==null){t.onBlock({mechanism:e,url:o,service:a});return}i.call(this,o)}}),()=>Object.defineProperty(r,"src",n)}function Mi(r){if(typeof window.fetch!="function")return()=>{};let e=window.fetch.bind(window);return window.fetch=function(n,i){let o;typeof n=="string"?o=n:n instanceof URL?o=n.href:o=n.url;let s=Te(o,r);return s!==null?(r.onBlock({mechanism:"fetch",url:o,service:s}),Promise.reject(new TypeError(`SimpleCMP: consent for ${s} not granted`))):e(n,i)},()=>{window.fetch=e}}function Ri(r){let e=XMLHttpRequest.prototype.open,t=XMLHttpRequest.prototype.send,n="__simplecmpBlockedService";return XMLHttpRequest.prototype.open=function(o,s,...a){let c=this;c[n]!==void 0&&delete c[n];let l=typeof s=="string"?s:s.href,f=Te(l,r);f!==null&&(r.onBlock({mechanism:"xhr",url:l,service:f}),c[n]=f),e.call(this,o,s,...a)},XMLHttpRequest.prototype.send=function(o){this[n]===void 0&&t.call(this,o)},()=>{XMLHttpRequest.prototype.open=e,XMLHttpRequest.prototype.send=t}}function Di(r){if(typeof navigator.sendBeacon!="function")return()=>{};let e=navigator.sendBeacon.bind(navigator);return navigator.sendBeacon=function(n,i){let o=typeof n=="string"?n:n.href,s=Te(o,r);return s!==null?(r.onBlock({mechanism:"sendBeacon",url:o,service:s}),!1):e(n,i)},()=>{navigator.sendBeacon=e}}function Qt(r,e={}){let t=r.filter(i=>Array.isArray(i.origins)&&i.origins.length>0).map(i=>({name:i.name,origins:i.origins})),n=e.blockAllUnknown===!0;return i=>{if(i==="")return null;for(let o of t)for(let s of o.origins)if(Ce(i,s))return o.name;return n?i:null}}var Me=globalThis,Re=Me.ShadowRoot&&(Me.ShadyCSS===void 0||Me.ShadyCSS.nativeShadow)&&"adoptedStyleSheets"in Document.prototype&&"replace"in CSSStyleSheet.prototype,tt=Symbol(),Xt=new WeakMap,oe=class{constructor(e,t,n){if(this._$cssResult$=!0,n!==tt)throw Error("CSSResult is not constructable. Use `unsafeCSS` or `css` instead.");this.cssText=e,this.t=t}get styleSheet(){let e=this.o,t=this.t;if(Re&&e===void 0){let n=t!==void 0&&t.length===1;n&&(e=Xt.get(t)),e===void 0&&((this.o=e=new CSSStyleSheet).replaceSync(this.cssText),n&&Xt.set(t,e))}return e}toString(){return this.cssText}},Yt=r=>new oe(typeof r=="string"?r:r+"",void 0,tt),y=(r,...e)=>{let t=r.length===1?r[0]:e.reduce((n,i,o)=>n+(s=>{if(s._$cssResult$===!0)return s.cssText;if(typeof s=="number")return s;throw Error("Value passed to 'css' function must be a 'css' function result: "+s+". Use 'unsafeCSS' to pass non-literal values, but take care to ensure page security.")})(i)+r[o+1],r[0]);return new oe(t,r,tt)},Zt=(r,e)=>{if(Re)r.adoptedStyleSheets=e.map(t=>t instanceof CSSStyleSheet?t:t.styleSheet);else for(let t of e){let n=document.createElement("style"),i=Me.litNonce;i!==void 0&&n.setAttribute("nonce",i),n.textContent=t.cssText,r.appendChild(n)}},nt=Re?r=>r:r=>r instanceof CSSStyleSheet?(e=>{let t="";for(let n of e.cssRules)t+=n.cssText;return Yt(t)})(r):r;var{is:Pi,defineProperty:Oi,getOwnPropertyDescriptor:Li,getOwnPropertyNames:Ni,getOwnPropertySymbols:Ui,getPrototypeOf:Ii}=Object,T=globalThis,en=T.trustedTypes,Bi=en?en.emptyScript:"",ji=T.reactiveElementPolyfillSupport,se=(r,e)=>r,ae={toAttribute(r,e){switch(e){case Boolean:r=r?Bi:null;break;case Object:case Array:r=r==null?r:JSON.stringify(r)}return r},fromAttribute(r,e){let t=r;switch(e){case Boolean:t=r!==null;break;case Number:t=r===null?null:Number(r);break;case Object:case Array:try{t=JSON.parse(r)}catch{t=null}}return t}},De=(r,e)=>!Pi(r,e),tn={attribute:!0,type:String,converter:ae,reflect:!1,useDefault:!1,hasChanged:De};Symbol.metadata??(Symbol.metadata=Symbol("metadata")),T.litPropertyMetadata??(T.litPropertyMetadata=new WeakMap);var C=class extends HTMLElement{static addInitializer(e){this._$Ei(),(this.l??(this.l=[])).push(e)}static get observedAttributes(){return this.finalize(),this._$Eh&&[...this._$Eh.keys()]}static createProperty(e,t=tn){if(t.state&&(t.attribute=!1),this._$Ei(),this.prototype.hasOwnProperty(e)&&((t=Object.create(t)).wrapped=!0),this.elementProperties.set(e,t),!t.noAccessor){let n=Symbol(),i=this.getPropertyDescriptor(e,n,t);i!==void 0&&Oi(this.prototype,e,i)}}static getPropertyDescriptor(e,t,n){let{get:i,set:o}=Li(this.prototype,e)??{get(){return this[t]},set(s){this[t]=s}};return{get:i,set(s){let a=i?.call(this);o?.call(this,s),this.requestUpdate(e,a,n)},configurable:!0,enumerable:!0}}static getPropertyOptions(e){return this.elementProperties.get(e)??tn}static _$Ei(){if(this.hasOwnProperty(se("elementProperties")))return;let e=Ii(this);e.finalize(),e.l!==void 0&&(this.l=[...e.l]),this.elementProperties=new Map(e.elementProperties)}static finalize(){if(this.hasOwnProperty(se("finalized")))return;if(this.finalized=!0,this._$Ei(),this.hasOwnProperty(se("properties"))){let t=this.properties,n=[...Ni(t),...Ui(t)];for(let i of n)this.createProperty(i,t[i])}let e=this[Symbol.metadata];if(e!==null){let t=litPropertyMetadata.get(e);if(t!==void 0)for(let[n,i]of t)this.elementProperties.set(n,i)}this._$Eh=new Map;for(let[t,n]of this.elementProperties){let i=this._$Eu(t,n);i!==void 0&&this._$Eh.set(i,t)}this.elementStyles=this.finalizeStyles(this.styles)}static finalizeStyles(e){let t=[];if(Array.isArray(e)){let n=new Set(e.flat(1/0).reverse());for(let i of n)t.unshift(nt(i))}else e!==void 0&&t.push(nt(e));return t}static _$Eu(e,t){let n=t.attribute;return n===!1?void 0:typeof n=="string"?n:typeof e=="string"?e.toLowerCase():void 0}constructor(){super(),this._$Ep=void 0,this.isUpdatePending=!1,this.hasUpdated=!1,this._$Em=null,this._$Ev()}_$Ev(){this._$ES=new Promise(e=>this.enableUpdating=e),this._$AL=new Map,this._$E_(),this.requestUpdate(),this.constructor.l?.forEach(e=>e(this))}addController(e){(this._$EO??(this._$EO=new Set)).add(e),this.renderRoot!==void 0&&this.isConnected&&e.hostConnected?.()}removeController(e){this._$EO?.delete(e)}_$E_(){let e=new Map,t=this.constructor.elementProperties;for(let n of t.keys())this.hasOwnProperty(n)&&(e.set(n,this[n]),delete this[n]);e.size>0&&(this._$Ep=e)}createRenderRoot(){let e=this.shadowRoot??this.attachShadow(this.constructor.shadowRootOptions);return Zt(e,this.constructor.elementStyles),e}connectedCallback(){this.renderRoot??(this.renderRoot=this.createRenderRoot()),this.enableUpdating(!0),this._$EO?.forEach(e=>e.hostConnected?.())}enableUpdating(e){}disconnectedCallback(){this._$EO?.forEach(e=>e.hostDisconnected?.())}attributeChangedCallback(e,t,n){this._$AK(e,n)}_$ET(e,t){let n=this.constructor.elementProperties.get(e),i=this.constructor._$Eu(e,n);if(i!==void 0&&n.reflect===!0){let o=(n.converter?.toAttribute!==void 0?n.converter:ae).toAttribute(t,n.type);this._$Em=e,o==null?this.removeAttribute(i):this.setAttribute(i,o),this._$Em=null}}_$AK(e,t){let n=this.constructor,i=n._$Eh.get(e);if(i!==void 0&&this._$Em!==i){let o=n.getPropertyOptions(i),s=typeof o.converter=="function"?{fromAttribute:o.converter}:o.converter?.fromAttribute!==void 0?o.converter:ae;this._$Em=i;let a=s.fromAttribute(t,o.type);this[i]=a??this._$Ej?.get(i)??a,this._$Em=null}}requestUpdate(e,t,n,i=!1,o){if(e!==void 0){let s=this.constructor;if(i===!1&&(o=this[e]),n??(n=s.getPropertyOptions(e)),!((n.hasChanged??De)(o,t)||n.useDefault&&n.reflect&&o===this._$Ej?.get(e)&&!this.hasAttribute(s._$Eu(e,n))))return;this.C(e,t,n)}this.isUpdatePending===!1&&(this._$ES=this._$EP())}C(e,t,{useDefault:n,reflect:i,wrapped:o},s){n&&!(this._$Ej??(this._$Ej=new Map)).has(e)&&(this._$Ej.set(e,s??t??this[e]),o!==!0||s!==void 0)||(this._$AL.has(e)||(this.hasUpdated||n||(t=void 0),this._$AL.set(e,t)),i===!0&&this._$Em!==e&&(this._$Eq??(this._$Eq=new Set)).add(e))}async _$EP(){this.isUpdatePending=!0;try{await this._$ES}catch(t){Promise.reject(t)}let e=this.scheduleUpdate();return e!=null&&await e,!this.isUpdatePending}scheduleUpdate(){return this.performUpdate()}performUpdate(){if(!this.isUpdatePending)return;if(!this.hasUpdated){if(this.renderRoot??(this.renderRoot=this.createRenderRoot()),this._$Ep){for(let[i,o]of this._$Ep)this[i]=o;this._$Ep=void 0}let n=this.constructor.elementProperties;if(n.size>0)for(let[i,o]of n){let{wrapped:s}=o,a=this[i];s!==!0||this._$AL.has(i)||a===void 0||this.C(i,void 0,o,a)}}let e=!1,t=this._$AL;try{e=this.shouldUpdate(t),e?(this.willUpdate(t),this._$EO?.forEach(n=>n.hostUpdate?.()),this.update(t)):this._$EM()}catch(n){throw e=!1,this._$EM(),n}e&&this._$AE(t)}willUpdate(e){}_$AE(e){this._$EO?.forEach(t=>t.hostUpdated?.()),this.hasUpdated||(this.hasUpdated=!0,this.firstUpdated(e)),this.updated(e)}_$EM(){this._$AL=new Map,this.isUpdatePending=!1}get updateComplete(){return this.getUpdateComplete()}getUpdateComplete(){return this._$ES}shouldUpdate(e){return!0}update(e){this._$Eq&&(this._$Eq=this._$Eq.forEach(t=>this._$ET(t,this[t]))),this._$EM()}updated(e){}firstUpdated(e){}};C.elementStyles=[],C.shadowRootOptions={mode:"open"},C[se("elementProperties")]=new Map,C[se("finalized")]=new Map,ji?.({ReactiveElement:C}),(T.reactiveElementVersions??(T.reactiveElementVersions=[])).push("2.1.2");var le=globalThis,nn=r=>r,Pe=le.trustedTypes,rn=Pe?Pe.createPolicy("lit-html",{createHTML:r=>r}):void 0,dn="$lit$",M=`lit$${Math.random().toFixed(9).slice(2)}$`,pn="?"+M,Hi=`<${pn}>`,j=document,de=()=>j.createComment(""),pe=r=>r===null||typeof r!="object"&&typeof r!="function",lt=Array.isArray,zi=r=>lt(r)||typeof r?.[Symbol.iterator]=="function",it=`[ 	
\f\r]`,ce=/<(?:(!--|\/[^a-zA-Z])|(\/?[a-zA-Z][^>\s]*)|(\/?$))/g,on=/-->/g,sn=/>/g,I=RegExp(`>|${it}(?:([^\\s"'>=/]+)(${it}*=${it}*(?:[^ 	
\f\r"'\`<>=]|("|')|))|$)`,"g"),an=/'/g,cn=/"/g,un=/^(?:script|style|textarea|title)$/i,dt=r=>(e,...t)=>({_$litType$:r,strings:e,values:t}),u=dt(1),Po=dt(2),Oo=dt(3),x=Symbol.for("lit-noChange"),d=Symbol.for("lit-nothing"),ln=new WeakMap,B=j.createTreeWalker(j,129);function fn(r,e){if(!lt(r)||!r.hasOwnProperty("raw"))throw Error("invalid template strings array");return rn!==void 0?rn.createHTML(e):e}var qi=(r,e)=>{let t=r.length-1,n=[],i,o=e===2?"<svg>":e===3?"<math>":"",s=ce;for(let a=0;a<t;a++){let c=r[a],l,f,p=-1,m=0;for(;m<c.length&&(s.lastIndex=m,f=s.exec(c),f!==null);)m=s.lastIndex,s===ce?f[1]==="!--"?s=on:f[1]!==void 0?s=sn:f[2]!==void 0?(un.test(f[2])&&(i=RegExp("</"+f[2],"g")),s=I):f[3]!==void 0&&(s=I):s===I?f[0]===">"?(s=i??ce,p=-1):f[1]===void 0?p=-2:(p=s.lastIndex-f[2].length,l=f[1],s=f[3]===void 0?I:f[3]==='"'?cn:an):s===cn||s===an?s=I:s===on||s===sn?s=ce:(s=I,i=void 0);let g=s===I&&r[a+1].startsWith("/>")?" ":"";o+=s===ce?c+Hi:p>=0?(n.push(l),c.slice(0,p)+dn+c.slice(p)+M+g):c+M+(p===-2?a:g)}return[fn(r,o+(r[t]||"<?>")+(e===2?"</svg>":e===3?"</math>":"")),n]},ue=class r{constructor({strings:e,_$litType$:t},n){let i;this.parts=[];let o=0,s=0,a=e.length-1,c=this.parts,[l,f]=qi(e,t);if(this.el=r.createElement(l,n),B.currentNode=this.el.content,t===2||t===3){let p=this.el.content.firstChild;p.replaceWith(...p.childNodes)}for(;(i=B.nextNode())!==null&&c.length<a;){if(i.nodeType===1){if(i.hasAttributes())for(let p of i.getAttributeNames())if(p.endsWith(dn)){let m=f[s++],g=i.getAttribute(p).split(M),_=/([.?@])?(.*)/.exec(m);c.push({type:1,index:o,name:_[2],strings:g,ctor:_[1]==="."?ot:_[1]==="?"?st:_[1]==="@"?at:Q}),i.removeAttribute(p)}else p.startsWith(M)&&(c.push({type:6,index:o}),i.removeAttribute(p));if(un.test(i.tagName)){let p=i.textContent.split(M),m=p.length-1;if(m>0){i.textContent=Pe?Pe.emptyScript:"";for(let g=0;g<m;g++)i.append(p[g],de()),B.nextNode(),c.push({type:2,index:++o});i.append(p[m],de())}}}else if(i.nodeType===8)if(i.data===pn)c.push({type:2,index:o});else{let p=-1;for(;(p=i.data.indexOf(M,p+1))!==-1;)c.push({type:7,index:o}),p+=M.length-1}o++}}static createElement(e,t){let n=j.createElement("template");return n.innerHTML=e,n}};function J(r,e,t=r,n){if(e===x)return e;let i=n!==void 0?t._$Co?.[n]:t._$Cl,o=pe(e)?void 0:e._$litDirective$;return i?.constructor!==o&&(i?._$AO?.(!1),o===void 0?i=void 0:(i=new o(r),i._$AT(r,t,n)),n!==void 0?(t._$Co??(t._$Co=[]))[n]=i:t._$Cl=i),i!==void 0&&(e=J(r,i._$AS(r,e.values),i,n)),e}var rt=class{constructor(e,t){this._$AV=[],this._$AN=void 0,this._$AD=e,this._$AM=t}get parentNode(){return this._$AM.parentNode}get _$AU(){return this._$AM._$AU}u(e){let{el:{content:t},parts:n}=this._$AD,i=(e?.creationScope??j).importNode(t,!0);B.currentNode=i;let o=B.nextNode(),s=0,a=0,c=n[0];for(;c!==void 0;){if(s===c.index){let l;c.type===2?l=new fe(o,o.nextSibling,this,e):c.type===1?l=new c.ctor(o,c.name,c.strings,this,e):c.type===6&&(l=new ct(o,this,e)),this._$AV.push(l),c=n[++a]}s!==c?.index&&(o=B.nextNode(),s++)}return B.currentNode=j,i}p(e){let t=0;for(let n of this._$AV)n!==void 0&&(n.strings!==void 0?(n._$AI(e,n,t),t+=n.strings.length-2):n._$AI(e[t])),t++}},fe=class r{get _$AU(){return this._$AM?._$AU??this._$Cv}constructor(e,t,n,i){this.type=2,this._$AH=d,this._$AN=void 0,this._$AA=e,this._$AB=t,this._$AM=n,this.options=i,this._$Cv=i?.isConnected??!0}get parentNode(){let e=this._$AA.parentNode,t=this._$AM;return t!==void 0&&e?.nodeType===11&&(e=t.parentNode),e}get startNode(){return this._$AA}get endNode(){return this._$AB}_$AI(e,t=this){e=J(this,e,t),pe(e)?e===d||e==null||e===""?(this._$AH!==d&&this._$AR(),this._$AH=d):e!==this._$AH&&e!==x&&this._(e):e._$litType$!==void 0?this.$(e):e.nodeType!==void 0?this.T(e):zi(e)?this.k(e):this._(e)}O(e){return this._$AA.parentNode.insertBefore(e,this._$AB)}T(e){this._$AH!==e&&(this._$AR(),this._$AH=this.O(e))}_(e){this._$AH!==d&&pe(this._$AH)?this._$AA.nextSibling.data=e:this.T(j.createTextNode(e)),this._$AH=e}$(e){let{values:t,_$litType$:n}=e,i=typeof n=="number"?this._$AC(e):(n.el===void 0&&(n.el=ue.createElement(fn(n.h,n.h[0]),this.options)),n);if(this._$AH?._$AD===i)this._$AH.p(t);else{let o=new rt(i,this),s=o.u(this.options);o.p(t),this.T(s),this._$AH=o}}_$AC(e){let t=ln.get(e.strings);return t===void 0&&ln.set(e.strings,t=new ue(e)),t}k(e){lt(this._$AH)||(this._$AH=[],this._$AR());let t=this._$AH,n,i=0;for(let o of e)i===t.length?t.push(n=new r(this.O(de()),this.O(de()),this,this.options)):n=t[i],n._$AI(o),i++;i<t.length&&(this._$AR(n&&n._$AB.nextSibling,i),t.length=i)}_$AR(e=this._$AA.nextSibling,t){for(this._$AP?.(!1,!0,t);e!==this._$AB;){let n=nn(e).nextSibling;nn(e).remove(),e=n}}setConnected(e){this._$AM===void 0&&(this._$Cv=e,this._$AP?.(e))}},Q=class{get tagName(){return this.element.tagName}get _$AU(){return this._$AM._$AU}constructor(e,t,n,i,o){this.type=1,this._$AH=d,this._$AN=void 0,this.element=e,this.name=t,this._$AM=i,this.options=o,n.length>2||n[0]!==""||n[1]!==""?(this._$AH=Array(n.length-1).fill(new String),this.strings=n):this._$AH=d}_$AI(e,t=this,n,i){let o=this.strings,s=!1;if(o===void 0)e=J(this,e,t,0),s=!pe(e)||e!==this._$AH&&e!==x,s&&(this._$AH=e);else{let a=e,c,l;for(e=o[0],c=0;c<o.length-1;c++)l=J(this,a[n+c],t,c),l===x&&(l=this._$AH[c]),s||(s=!pe(l)||l!==this._$AH[c]),l===d?e=d:e!==d&&(e+=(l??"")+o[c+1]),this._$AH[c]=l}s&&!i&&this.j(e)}j(e){e===d?this.element.removeAttribute(this.name):this.element.setAttribute(this.name,e??"")}},ot=class extends Q{constructor(){super(...arguments),this.type=3}j(e){this.element[this.name]=e===d?void 0:e}},st=class extends Q{constructor(){super(...arguments),this.type=4}j(e){this.element.toggleAttribute(this.name,!!e&&e!==d)}},at=class extends Q{constructor(e,t,n,i,o){super(e,t,n,i,o),this.type=5}_$AI(e,t=this){if((e=J(this,e,t,0)??d)===x)return;let n=this._$AH,i=e===d&&n!==d||e.capture!==n.capture||e.once!==n.once||e.passive!==n.passive,o=e!==d&&(n===d||i);i&&this.element.removeEventListener(this.name,this,n),o&&this.element.addEventListener(this.name,this,e),this._$AH=e}handleEvent(e){typeof this._$AH=="function"?this._$AH.call(this.options?.host??this.element,e):this._$AH.handleEvent(e)}},ct=class{constructor(e,t,n){this.element=e,this.type=6,this._$AN=void 0,this._$AM=t,this.options=n}get _$AU(){return this._$AM._$AU}_$AI(e){J(this,e)}};var Fi=le.litHtmlPolyfillSupport;Fi?.(ue,fe),(le.litHtmlVersions??(le.litHtmlVersions=[])).push("3.3.2");var mn=(r,e,t)=>{let n=t?.renderBefore??e,i=n._$litPart$;if(i===void 0){let o=t?.renderBefore??null;n._$litPart$=i=new fe(e.insertBefore(de(),o),o,void 0,t??{})}return i._$AI(r),i};var me=globalThis,R=class extends C{constructor(){super(...arguments),this.renderOptions={host:this},this._$Do=void 0}createRenderRoot(){var t;let e=super.createRenderRoot();return(t=this.renderOptions).renderBefore??(t.renderBefore=e.firstChild),e}update(e){let t=this.render();this.hasUpdated||(this.renderOptions.isConnected=this.isConnected),super.update(e),this._$Do=mn(t,this.renderRoot,this.renderOptions)}connectedCallback(){super.connectedCallback(),this._$Do?.setConnected(!0)}disconnectedCallback(){super.disconnectedCallback(),this._$Do?.setConnected(!1)}render(){return x}};R._$litElement$=!0,R.finalized=!0,me.litElementHydrateSupport?.({LitElement:R});var Wi=me.litElementPolyfillSupport;Wi?.({LitElement:R});(me.litElementVersions??(me.litElementVersions=[])).push("4.2.2");var k=r=>(e,t)=>{t!==void 0?t.addInitializer(()=>{customElements.define(r,e)}):customElements.define(r,e)};var Vi={attribute:!0,type:String,converter:ae,reflect:!1,hasChanged:De},Gi=(r=Vi,e,t)=>{let{kind:n,metadata:i}=t,o=globalThis.litPropertyMetadata.get(i);if(o===void 0&&globalThis.litPropertyMetadata.set(i,o=new Map),n==="setter"&&((r=Object.create(r)).wrapped=!0),o.set(t.name,r),n==="accessor"){let{name:s}=t;return{set(a){let c=e.get.call(this);e.set.call(this,a),this.requestUpdate(s,c,r,!0,a)},init(a){return a!==void 0&&this.C(s,void 0,r,a),a}}}if(n==="setter"){let{name:s}=t;return function(a){let c=this[s];e.call(this,a),this.requestUpdate(s,c,r,!0,a)}}throw Error("Unsupported decorator location: "+n)};function v(r){return(e,t)=>typeof t=="object"?Gi(r,e,t):((n,i,o)=>{let s=i.hasOwnProperty(o);return i.constructor.createProperty(o,n),s?Object.getOwnPropertyDescriptor(i,o):void 0})(r,e,t)}function Oe(r){return v({...r,state:!0,attribute:!1})}var H=(r,e,t)=>(t.configurable=!0,t.enumerable=!0,Reflect.decorate&&typeof e!="object"&&Object.defineProperty(r,e,t),t);function Le(r,e){return(t,n,i)=>{let o=s=>s.renderRoot?.querySelector(r)??null;if(e){let{get:s,set:a}=typeof n=="object"?t:i??(()=>{let c=Symbol();return{get(){return this[c]},set(l){this[c]=l}}})();return H(t,n,{get(){let c=s.call(this);return c===void 0&&(c=o(this),(c!==null||this.hasUpdated)&&a.call(this,c)),c}})}return H(t,n,{get(){return o(this)}})}}var pt=r=>r??d;var hn={ATTRIBUTE:1,CHILD:2,PROPERTY:3,BOOLEAN_ATTRIBUTE:4,EVENT:5,ELEMENT:6},gn=r=>(...e)=>({_$litDirective$:r,values:e}),Ne=class{constructor(e){}get _$AU(){return this._$AM._$AU}_$AT(e,t,n){this._$Ct=e,this._$AM=t,this._$Ci=n}_$AS(e,t){return this.update(e,t)}update(e,t){return this.render(...t)}};var he=class extends Ne{constructor(e){if(super(e),this.it=d,e.type!==hn.CHILD)throw Error(this.constructor.directiveName+"() can only be used in child bindings")}render(e){if(e===d||e==null)return this._t=void 0,this.it=e;if(e===x)return e;if(typeof e!="string")throw Error(this.constructor.directiveName+"() called with a non-string value");if(e===this.it)return this._t;this.it=e;let t=[e];return t.raw=t,this._t={_$litType$:this.constructor.resultType,strings:t,values:[]}}};he.directiveName="unsafeHTML",he.resultType=1;var X=gn(he);function A(r){return r.split("-").map(e=>e.slice(0,1).toUpperCase()+e.slice(1)).join(" ")}function vn(r){let e=jt(r),t=Ke(r),n=r.fallbackLang??"zz";return(i,...o)=>Ut(e,t,n,i,...o)}function Ki(r){return{update:()=>{r.requestUpdate()}}}var b=class extends R{_t(e,...t){if(this._translator===void 0){if(this.config===void 0)return e;this._translator=vn(this.config)}return this._translator(e,...t)}createRenderRoot(){return this.getAttribute("mode")==="light"?this:super.createRenderRoot()}_detachWatcher(){this._watcher!==void 0&&this._watcherManager!==void 0&&this._watcherManager.unwatch(this._watcher),this._watcher=void 0,this._watcherManager=void 0}_syncWatcher(){this._watcherManager!==this.manager&&(this._detachWatcher(),this.manager!==void 0&&(this._watcher=Ki(this),this._watcherManager=this.manager,this.manager.watch(this._watcher)))}connectedCallback(){super.connectedCallback(),this._syncWatcher()}disconnectedCallback(){this._detachWatcher(),super.disconnectedCallback()}willUpdate(e){super.willUpdate(e),e.has("config")&&(this._translator=void 0),e.has("manager")&&this._syncWatcher()}_emit(e,t){this.dispatchEvent(new CustomEvent(`simplecmp:${e}`,{detail:t,bubbles:!0,composed:!0}))}};h([v({attribute:!1})],b.prototype,"config",2),h([v({attribute:!1})],b.prototype,"manager",2);var Ji=y`
  :host {
    --simplecmp-color-primary: #15775a;
    --simplecmp-color-primary-hover: #0f5d44;
    --simplecmp-color-secondary: #6c757d;
    --simplecmp-color-danger: #da2c43;
    --simplecmp-color-bg: #ffffff;
    --simplecmp-color-bg-alt: #f5f7f9;
    --simplecmp-color-border: #dde2e7;
    --simplecmp-color-text: #1a232c;
    --simplecmp-color-text-muted: #5f6b78;

    --simplecmp-radius: 6px;
    --simplecmp-spacing: 0.75rem;
    --simplecmp-spacing-sm: 0.5rem;
    --simplecmp-spacing-lg: 1.25rem;

    --simplecmp-font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
    --simplecmp-font-family-heading: var(--simplecmp-font-family);
    --simplecmp-font-size: 0.95rem;
    --simplecmp-font-size-heading: 20px;
    --simplecmp-font-size-sm: 0.85rem;
    --simplecmp-line-height: 1.5;

    --simplecmp-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    --simplecmp-z-index: 2147483000;
  }

  @media (prefers-reduced-motion: reduce) {
    :host {
      --simplecmp-transition: none;
    }
  }
`,ge=y`
  :host {
    color: var(--simplecmp-color-text, #1a232c);
    font-family: var(--simplecmp-font-family, system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif);
    font-size: var(--simplecmp-font-size, 0.95rem);
    line-height: var(--simplecmp-line-height, 1.5);
  }
`,w=y`
  ${Ji}
  ${ge}
`;function Qi(r,e){if(r!==void 0){if(typeof r=="string")return r;if(typeof r=="object")return r[e]??r.default}}var D=class extends b{constructor(){super(...arguments);this.testing=!1;this._previouslyFocused=null;this._handleAccept=()=>{this.manager!==void 0&&(this.manager.changeAll(!0),this.manager.saveAndApplyConsents("accept"),this._emit("accept"))};this._handleDecline=()=>{this.manager!==void 0&&(this.manager.changeAll(!1),this.manager.saveAndApplyConsents("decline"),this._emit("decline"))};this._handleConfigure=t=>{t.preventDefault(),this._emit("configure")}}connectedCallback(){super.connectedCallback(),this.config?.autoFocus===!0&&(this._previouslyFocused=typeof document<"u"?document.activeElement:null,queueMicrotask(()=>{this.renderRoot?.querySelector(".cn-body")?.focus()}))}disconnectedCallback(){super.disconnectedCallback();let t=this._previouslyFocused;this._previouslyFocused=null,t&&typeof t.focus=="function"&&t.isConnected&&t.focus()}render(){let t=this.config,n=this.manager;if(t===void 0||n===void 0)return d;if(!this.testing&&n.confirmed)return d;if(t.noNotice===!0)return d;let i=n.bannerMode==="notice",o=this._activeLang(),s=this._resolvePolicyUrl(t.privacyPolicy,["privacyPolicyUrl"],o),a=this._resolvePolicyUrl(t.imprint,["imprintUrl"],o),c=this._t(["!","consentNotice","title"]),l=t.showNoticeTitle===!0&&c!==void 0,f=t.htmlTexts===!0,p=s?u`<a href=${s}>${this._t(["privacyPolicy","name"])}</a>`:"",m=a?u`<a href=${a}>${this._imprintLinkText()}</a>`:"",g=u`<a
      href="#"
      @click=${this._handleConfigure}
      >${this._t(["consentNotice","learnMore"])}</a
    >`,_=this._t(i?["consentNotice","optOutDescription"]:["consentNotice","description"],{purposes:u`<strong>${this._purposesText(t)}</strong>`,privacyPolicy:p,imprint:m,learnMoreLink:g});return u`
      <div
        class="cn-body"
        role="region"
        aria-live="polite"
        aria-labelledby=${pt(l?"cn-title":void 0)}
        aria-label=${pt(l?void 0:c)}
        aria-describedby="cn-description"
        tabindex="-1"
      >
        ${l?u`<h2 id="cn-title">${c}</h2>`:d}
        <p id="cn-description">${f?Xi(_):_}</p>
        ${this._renderPolicyLinks(s,a)}
        ${n.changed?u`<p class="cn-changes">${this._t(["consentNotice","changeDescription"])}</p>`:d}
        ${this.testing?u`<p>${this._t(["consentNotice","testing"])}</p>`:d}
        ${i?this._renderNoticeButtons(t):this._renderButtonRow(t)}
      </div>
    `}_renderNoticeButtons(t){let n=this._resolveLayout(t),i=t.hideLearnMore!==!0;return u`<div class="cn-buttons cn-layout-${n}">
      ${i?u`<button
            type="button"
            class="cn-configure"
            @click=${this._handleConfigure}
          >
            ${this._t(["consentNotice","learnMore"])}
          </button>`:d}
      <button type="button" class="cn-decline" @click=${this._handleDecline}>
        ${this._t(["doNotSell"])}
      </button>
      <button type="button" class="cn-accept" @click=${this._handleAccept}>
        ${this._t(["ok"])}
      </button>
    </div>`}_renderButtonRow(t){let n=this._resolveLayout(t),i=n!=="compact"&&t.hideLearnMore!==!0,o=t.hideDeclineAll!==!0;return u`<div class="cn-buttons cn-layout-${n}">
      ${i?u`<button
            type="button"
            class="cn-configure"
            @click=${this._handleConfigure}
          >
            ${this._t(["consentNotice","learnMore"])}
          </button>`:d}
      ${o?u`<button
            type="button"
            class="cn-decline"
            @click=${this._handleDecline}
          >
            ${this._t(["decline"])}
          </button>`:d}
      <button type="button" class="cn-accept" @click=${this._handleAccept}>
        ${this._t(["ok"])}
      </button>
    </div>`}_resolveLayout(t){let n=(t.layout??"standard").toString().toLowerCase();return n==="compact"||n==="stacked"?n:"standard"}_activeLang(){return this.config?.lang??document.documentElement.lang??"en"}_purposesText(t){let n=t.purposeOrder??[],o=Lt(t).filter(c=>c!=="functional").sort((c,l)=>n.indexOf(c)-n.indexOf(l)).map(c=>this._tString(["!","purposes",c,"title?"])||A(c));if(o.length<=1)return o[0]??"";let s=o.slice(0,-2),a=o.slice(-2).join(" & ");return[...s,a].join(", ")}_resolvePolicyUrl(t,n,i){let o=Qi(t,i);if(o!==void 0)return o;let s=this._tString(["!",...n]);return s===""?void 0:s}_imprintLinkText(){return this._tString(["!","consentNotice","imprint","name"])||this._tString(["!","imprint","name"])||"Imprint"}_tString(t){let n=this._t(t);return typeof n=="string"?n:Array.isArray(n)?n.map(i=>typeof i=="string"?i:"").join(""):""}_renderPolicyLinks(t,n){return t===void 0&&n===void 0?d:u`
      <p class="cn-policy-links">
        ${t?u`<a href=${t}>${this._t(["privacyPolicy","name"])}</a>`:d}
        ${t&&n?" \xB7 ":d}
        ${n?u`<a href=${n}>${this._imprintLinkText()}</a>`:d}
      </p>
    `}};D.styles=[w,y`
      :host {
        display: block;
        position: fixed;
        /* Banner placement — overridable via three tokens, all set
           together by the integrator (t3-simplecmp ships a 3x3
           picker). Default mirrors the original hard-coded
           bottom-right corner. */
        inset: var(
          --simplecmp-banner-inset,
          auto var(--simplecmp-spacing) var(--simplecmp-spacing) auto
        );
        transform: var(--simplecmp-banner-transform, none);
        max-width: var(--simplecmp-banner-max-width, 30rem);
        z-index: var(--simplecmp-z-index);
      }

      :host([hidden]) {
        display: none;
      }

      .cn-body {
        background: var(--simplecmp-color-bg);
        color: var(--simplecmp-color-text);
        border: 1px solid var(--simplecmp-color-border);
        border-radius: var(--simplecmp-radius);
        box-shadow: var(--simplecmp-shadow);
        padding: var(--simplecmp-spacing-lg);
      }

      h2 {
        margin: 0 0 var(--simplecmp-spacing) 0;
        font-family: var(--simplecmp-font-family-heading);
        font-size: var(--simplecmp-font-size-heading);
      }

      p {
        margin: 0 0 var(--simplecmp-spacing) 0;
      }

      .cn-policy-links {
        font-size: var(--simplecmp-font-size-sm);
        color: var(--simplecmp-color-text-muted);
      }

      .cn-policy-links a {
        color: var(--simplecmp-color-text-muted);
      }

      /* Layout-independent button row defaults. Per-layout overrides
         live in the .cn-layout-* selectors below.

         Compliance baseline (legal-compliance.md §1.2 + §2.3): all
         three buttons share identical styling. Visual hierarchy is
         carried by label + position, never by color/size/weight.
         The previous Accept-filled-primary vs. Decline-ghost-outline
         treatment was a Stirring dark pattern; this rewrite levels
         the visual playing field. */
      .cn-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: var(--simplecmp-spacing-sm);
        margin-top: var(--simplecmp-spacing);
      }

      button {
        font: inherit;
        font-weight: 500;
        border: 1px solid var(--simplecmp-color-border);
        border-radius: var(--simplecmp-radius);
        padding: var(--simplecmp-spacing-sm) var(--simplecmp-spacing);
        cursor: pointer;
        background: var(--simplecmp-color-bg-alt);
        color: var(--simplecmp-color-text);
        line-height: var(--simplecmp-line-height);
        /* WCAG 2.5.8 (AA) — minimum 24×24 target. */
        min-block-size: 24px;
        min-inline-size: 24px;
      }

      button:hover {
        background: var(--simplecmp-color-border);
      }

      button:focus-visible {
        outline: 2px solid var(--simplecmp-color-primary);
        outline-offset: 2px;
      }

      /* Standard layout — horizontal flex row with Configure-Decline-
         Accept in source order. Wraps on narrow viewports. */
      .cn-layout-standard {
        /* Inherits .cn-buttons defaults — explicit name retained for
           future per-layout tweaks without bloating the base rule. */
      }

      /* Compact layout — Decline | Accept only, no Configure. The
         component skips the Configure button in render() when
         layout === 'compact'; this rule just exists so a
         downstream theme can target compact-mode if needed. */
      .cn-layout-compact {
        /* same as standard for now */
      }

      /* Stacked layout — vertical column, each button full-width.
         Optimised for narrow viewports and assistive tech where
         buttons-of-equal-styling on a row are hard to scan. */
      .cn-layout-stacked {
        flex-direction: column;
        align-items: stretch;
      }

      .cn-layout-stacked button {
        width: 100%;
      }

      a {
        color: var(--simplecmp-color-primary);
      }

      .cn-changes {
        font-size: var(--simplecmp-font-size-sm);
        font-style: italic;
        color: var(--simplecmp-color-text-muted);
      }
    `],h([v({type:Boolean})],D.prototype,"testing",2),D=h([k("simplecmp-banner")],D);function Xi(r){return typeof r=="string"?X(r):Array.isArray(r)?r.map(e=>typeof e=="string"?X(e):e):r}function ve(r){let e;try{e=new URL(r,document.baseURI)}catch{return!1}return e.protocol==="http:"||e.protocol==="https:"}var P=class extends b{constructor(){super(...arguments);this.open=!1;this._onCancel=()=>{};this._onClose=()=>{this.open=!1,this._emit("provider-info-close")};this._onCloseClick=()=>{this.open=!1,this._emit("provider-info-close")};this._onBackdropClick=t=>{t.target===this._dialog&&(this.open=!1,this._emit("provider-info-close"))}}updated(t){if(super.updated(t),t.has("open")){let n=this._dialog;if(n===void 0)return;this.open&&!n.open?n.showModal():!this.open&&n.open&&n.close()}}render(){let t=this.service;if(t===void 0)return d;let n=this._tString(["providerInfo","close"])||"Close";return u`
      <dialog
        aria-labelledby="simplecmp-provider-info-title"
        @cancel=${this._onCancel}
        @close=${this._onClose}
        @click=${this._onBackdropClick}
      >
        <header>
          <h2 id="simplecmp-provider-info-title">
            ${this._tString(["providerInfo","title"])||"Provider information"}
          </h2>
          <button type="button" class="close" @click=${this._onCloseClick} aria-label=${n}>
            ×
          </button>
        </header>
        <div class="body">${this._renderBody(t)}</div>
        <footer>
          <button type="button" @click=${this._onCloseClick}>${n}</button>
        </footer>
      </dialog>
    `}_renderBody(t){let n=[],i=(o,s,a=!1)=>{if(s===void 0||s==="")return;let c=this._tString(["providerInfo","field",o])||o,l=a&&ve(s)?u`<a href=${s} target="_blank" rel="noopener noreferrer">${s}</a>`:u`${s}`;n.push(u`<dt>${c}</dt><dd>${l}</dd>`)};return i("vendor",t.vendor),i("description",t.vendorDescription),i("address",t.vendorAddress),i("country",t.vendorCountry),i("privacyPolicy",t.privacyPolicyUrl,!0),i("optOut",t.vendorOptOutUrl,!0),i("partner",t.vendorPartner),n.length===0?u`<p class="empty">
        ${this._tString(["providerInfo","noData"])||"No provider information available."}
      </p>`:u`<dl>${n}</dl>`}_tString(t){let n=this._t(t);return typeof n=="string"?n:Array.isArray(n)?n.map(i=>typeof i=="string"?i:"").join(""):""}};P.styles=[w,y`
      :host {
        display: contents;
      }

      dialog {
        max-width: 36rem;
        width: 90%;
        border: 1px solid var(--simplecmp-color-border);
        border-radius: var(--simplecmp-radius);
        padding: 0;
        background: var(--simplecmp-color-bg);
        color: var(--simplecmp-color-text);
        font-family: var(--simplecmp-font-family);
        font-size: var(--simplecmp-font-size);
      }

      dialog::backdrop {
        background: rgba(0, 0, 0, 0.4);
      }

      header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: var(--simplecmp-spacing-lg);
        border-bottom: 1px solid var(--simplecmp-color-border);
      }

      header h2 {
        font-size: var(--simplecmp-font-size-heading, 1.25rem);
        font-family: var(--simplecmp-font-family-heading, var(--simplecmp-font-family));
        margin: 0;
      }

      button.close {
        font: inherit;
        background: transparent;
        border: none;
        color: var(--simplecmp-color-text-muted);
        cursor: pointer;
        font-size: 1.5rem;
        line-height: 1;
        padding: 0 var(--simplecmp-spacing-sm);
      }

      button.close:hover {
        color: var(--simplecmp-color-text);
      }

      .body {
        padding: var(--simplecmp-spacing-lg);
      }

      dl {
        margin: 0;
        display: grid;
        grid-template-columns: max-content 1fr;
        gap: var(--simplecmp-spacing-sm) var(--simplecmp-spacing);
      }

      dt {
        font-weight: 600;
        color: var(--simplecmp-color-text-muted);
        white-space: nowrap;
      }

      dd {
        margin: 0;
        word-break: break-word;
      }

      dd a {
        color: var(--simplecmp-color-primary);
        text-decoration: underline;
      }

      dd a:hover {
        color: var(--simplecmp-color-primary-hover);
      }

      .empty {
        font-style: italic;
        color: var(--simplecmp-color-text-muted);
      }

      footer {
        display: flex;
        justify-content: flex-end;
        padding: var(--simplecmp-spacing) var(--simplecmp-spacing-lg);
        border-top: 1px solid var(--simplecmp-color-border);
      }

      footer button {
        font: inherit;
        border: 1px solid transparent;
        border-radius: var(--simplecmp-radius);
        padding: var(--simplecmp-spacing-sm) var(--simplecmp-spacing);
        cursor: pointer;
        background: var(--simplecmp-color-primary);
        color: white;
      }

      footer button:hover {
        background: var(--simplecmp-color-primary-hover);
      }
    `],h([v({attribute:!1})],P.prototype,"service",2),h([v({type:Boolean,reflect:!0})],P.prototype,"open",2),h([Le("dialog")],P.prototype,"_dialog",2),P=h([k("simplecmp-provider-info-modal")],P);var O=class extends b{constructor(){super(...arguments);this._autoPlaceholder=!1;this._providerInfoOpen=!1;this._onAcceptOnce=()=>{let t=this._resolveService();t===void 0||this.manager===void 0||(this.manager.updateConsent(t.name,!0),this.manager.applyConsents(!1,!0,t.name),this.manager.updateConsent(t.name,!1),this._emit("contextual-accept-once",{name:t.name}))};this._onAccept=()=>{let t=this._resolveService();t===void 0||this.manager===void 0||(this.manager.updateConsent(t.name,!0),this.manager.confirmed?(this.manager.saveConsents("contextual-accept"),this.manager.applyConsents(!1,!0,t.name)):this.manager.applyConsents(!1,!0,t.name),this._emit("contextual-accept",{name:t.name}))};this._onConfigure=t=>{t.preventDefault(),this._emit("configure")};this._onProviderInfoOpen=t=>{t.preventDefault(),this._providerInfoOpen=!0};this._onProviderInfoClose=()=>{this._providerInfoOpen=!1}}connectedCallback(){super.connectedCallback(),this._autoPlaceholder=this.hasAttribute("data-simplecmp-auto-placeholder"),this.hasAttribute("role")||this.setAttribute("role","region")}_resolveService(){if(this.service!==void 0)return this.service;if(this.serviceName!==void 0){if(this.config!==void 0){let t=this.config.services.find(n=>n.name===this.serviceName);if(t!==void 0)return t}return{name:this.serviceName,purposes:[]}}}_renderMode(){let t=this.service?.name??this.serviceName;return t===void 0||this.config?.services.some(i=>i.name===t)===!0?"configured":this.getAttribute("data-blocked-source")==="host"?"host":"library"}render(){let t=this._resolveService();if(t===void 0||this.manager===void 0)return d;let n=this._renderMode(),i=n==="host"?t.name:this._resolveTitle(t),o=n==="host"?this._t(["contextualConsent","descriptionUnknownHost"],{title:i}):this._resolveDescription(t,i);if(n==="host")return u`<p>${o}</p>`;let s=this.manager.store.get()!==null,a=n==="configured"&&s,c=n==="configured";return u`
      <p>${o}</p>
      ${this._renderPurposes(t)}
      ${this._renderProviderInfoLink(t)}
      <div class="buttons">
        <button type="button" class="accept-once" @click=${this._onAcceptOnce}>
          ${this._t(["contextualConsent","acceptOnce"])}
        </button>
        ${a?u`<button type="button" class="accept" @click=${this._onAccept}>
              ${this._t(["contextualConsent","acceptAlways"])}
            </button>`:d}
        ${c?u`<button type="button" class="configure" @click=${this._onConfigure}>
              ${this._t(["contextualConsent","modalLinkText"])}
            </button>`:d}
      </div>
      ${this._renderProviderInfoModal(t)}
    `}_renderProviderInfoLink(t){return this._hasProviderData(t)?u`
      <p class="provider-info-link">
        <a href="#" @click=${this._onProviderInfoOpen}>
          ${this._tString(["contextualConsent","providerInfoLink"])||"More information \u203A"}
        </a>
      </p>
    `:d}_renderProviderInfoModal(t){return this._providerInfoOpen?u`
      <simplecmp-provider-info-modal
        .service=${this._resolveProviderService(t)}
        .config=${this.config}
        .manager=${this.manager}
        ?open=${this._providerInfoOpen}
        @simplecmp:provider-info-close=${this._onProviderInfoClose}
      ></simplecmp-provider-info-modal>
    `:d}_resolveProviderService(t){let n=this.config?.libraryFallback?.[t.name];return n===void 0?t:{...t,vendor:t.vendor??n.vendor,vendorCountry:t.vendorCountry??n.vendorCountry,vendorAddress:t.vendorAddress??n.vendorAddress,vendorOptOutUrl:t.vendorOptOutUrl??n.vendorOptOutUrl,vendorPartner:t.vendorPartner??n.vendorPartner,vendorDescription:t.vendorDescription??n.vendorDescription,privacyPolicyUrl:t.privacyPolicyUrl??n.privacyPolicyUrl}}_hasProviderData(t){let n=this._resolveProviderService(t);return!!(n.vendor||n.vendorCountry||n.vendorAddress||n.vendorOptOutUrl||n.vendorPartner||n.vendorDescription||n.privacyPolicyUrl)}_renderPurposes(t){let n=t.purposes??[];if(n.length===0&&this.config!==void 0){let a=this.config.libraryFallback?.[t.name];a?.purposes!==void 0&&(n=a.purposes)}if(n.length===0)return d;let i=n.map(s=>this._tString(["purposes",s,"title"])).filter(s=>s.length>0);if(i.length===0)return d;let o=this._tString(["service","purposes"])||"Purposes";return u`<p class="purposes">${o}: ${i.join(", ")}</p>`}_resolveTitle(t){let n=this.getAttribute("data-simplecmp-title");return n!==null&&n.length>0?n:typeof t.placeholderTitle=="string"&&t.placeholderTitle.length>0?t.placeholderTitle:this._tString(["!",t.name,"placeholderTitle?"])||this._tString(["!",t.name,"title?"])||A(t.name)}_resolveDescription(t,n){let i=this.getAttribute("data-simplecmp-description");if(i!==null&&i.length>0)return i;if(typeof t.placeholderDescription=="string"&&t.placeholderDescription.length>0)return t.placeholderDescription;let o=this._tString(["!",t.name,"placeholderDescription?"]);return o!==""?o:this._t(["contextualConsent","description"],{title:n})}firstUpdated(t){super.firstUpdated?.(t),this._updateAriaLabel(),this._maybeFocusFirstAction()}updated(t){super.updated?.(t),this._updateAriaLabel()}_updateAriaLabel(){let t=this._resolveService();if(t===void 0)return;let n=this._resolveTitle(t),i=this._tString(["!","contextualConsent","ariaLabel?"])||n,o=i.includes("{title}")?i.replace("{title}",n):i;this.getAttribute("aria-label")!==o&&this.setAttribute("aria-label",o)}_maybeFocusFirstAction(){if(!this._autoPlaceholder)return;this.renderRoot.querySelector("button:not([disabled])")?.focus()}_tString(t){let n=this._t(t);return typeof n=="string"?n:Array.isArray(n)?n.map(i=>typeof i=="string"?i:"").join(""):""}};O.styles=[w,y`
      :host {
        /*
         * Flex column with content centered along the cross axis fills
         * the host when a parent constrains its dimensions (e.g.
         * Bootstrap's \`.ratio ratio-16x9\` wrapper that absolute-
         * positions children to 640×360), and shrinks to natural
         * content size when nothing constrains it. Prevents the
         * "compact notice bar at top, ~300px white below" layout the
         * universal-blocking rewriter would otherwise produce inside
         * aspect-ratio wrappers.
         */
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: var(--simplecmp-spacing-lg);
        background: var(--simplecmp-color-bg-alt);
        border: 1px solid var(--simplecmp-color-border);
        border-radius: var(--simplecmp-radius);
        color: var(--simplecmp-color-text);
        box-sizing: border-box;
      }

      p {
        margin: 0 0 var(--simplecmp-spacing) 0;
      }

      p.purposes {
        font-size: 0.875em;
        color: var(--simplecmp-color-text-muted);
      }

      .buttons {
        display: flex;
        flex-wrap: wrap;
        gap: var(--simplecmp-spacing-sm);
      }

      button {
        font: inherit;
        border: 1px solid transparent;
        border-radius: var(--simplecmp-radius);
        padding: var(--simplecmp-spacing-sm) var(--simplecmp-spacing);
        cursor: pointer;
      }

      button.accept {
        background: var(--simplecmp-color-primary);
        color: white;
      }

      button.accept:hover {
        background: var(--simplecmp-color-primary-hover);
      }

      button.accept-once {
        background: transparent;
        color: var(--simplecmp-color-primary);
        border-color: var(--simplecmp-color-primary);
      }

      button.configure {
        background: transparent;
        color: var(--simplecmp-color-text);
        border-color: var(--simplecmp-color-border);
      }

      .provider-info-link {
        font-size: 0.875em;
        margin: 0 0 var(--simplecmp-spacing) 0;
      }

      .provider-info-link a {
        color: var(--simplecmp-color-primary);
        text-decoration: underline;
        cursor: pointer;
      }

      .provider-info-link a:hover {
        color: var(--simplecmp-color-primary-hover);
      }
    `],h([v({attribute:!1})],O.prototype,"service",2),h([v({type:String,attribute:"service-name"})],O.prototype,"serviceName",2),h([Oe()],O.prototype,"_providerInfoOpen",2),O=h([k("simplecmp-contextual-notice")],O);var z=class extends b{constructor(){super(...arguments);this.visible=!0;this._onChange=t=>{let n=t.target.checked,i=this.service;i===void 0||i.required||(this.manager?.updateConsent(i.name,n),this._emit("service-toggle",{name:i.name,value:n}))}}render(){let t=this.service;if(t===void 0)return d;let n=`simplecmp-service-${t.name}`,i=t.required===!0||this.manager?.consents[t.name]===!0,o=this._tString(["!",t.name,"title?"])||A(t.name),s=this._tString(["!",t.name,"description?"])||void 0;return u`
      <div class="row">
        <input
          type="checkbox"
          id=${n}
          .checked=${i}
          ?disabled=${t.required===!0}
          tabindex=${this.visible?"0":"-1"}
          @change=${this._onChange}
        />
        <div class="meta">
          <label for=${n}>
            <span class="title">${o}</span>
            ${t.required?u`<span class="badge">${this._t(["service","required","title"])}</span>`:d}
            ${t.optOut?u`<span class="badge">${this._t(["service","optOut","title"])}</span>`:d}
          </label>
          ${s?u`<p class="description">${s}</p>`:d}
          ${this._renderPurposes(t)}
        </div>
      </div>
    `}_renderPurposes(t){let n=t.purposes??[];if(n.length===0)return d;let i=n.map(s=>this._tString(["!","purposes",s,"title?"])||A(s)).join(", "),o=this._t(["service",n.length>1?"purposes":"purpose"]);return u`<p class="purposes">${o}: ${i}</p>`}_tString(t){let n=this._t(t);return typeof n=="string"?n:Array.isArray(n)?n.map(i=>typeof i=="string"?i:"").join(""):""}};z.styles=[ge,y`
      :host {
        display: block;
        margin: var(--simplecmp-spacing-sm) 0;
      }

      .row {
        display: flex;
        align-items: flex-start;
        gap: var(--simplecmp-spacing-sm);
      }

      input[type='checkbox'] {
        margin-top: 0.25rem;
        flex-shrink: 0;
        accent-color: var(--simplecmp-color-primary);
      }

      .meta {
        flex: 1;
      }

      .title {
        font-weight: 500;
      }

      .badge {
        display: inline-block;
        margin-left: var(--simplecmp-spacing-sm);
        padding: 0 0.4rem;
        font-size: var(--simplecmp-font-size-sm);
        background: var(--simplecmp-color-bg-alt);
        border-radius: var(--simplecmp-radius);
        color: var(--simplecmp-color-text-muted);
      }

      .description {
        margin: 0.25rem 0 0 0;
        font-size: var(--simplecmp-font-size-sm);
        color: var(--simplecmp-color-text-muted);
      }

      .purposes {
        margin: 0.25rem 0 0 0;
        font-size: var(--simplecmp-font-size-sm);
        color: var(--simplecmp-color-text-muted);
      }
    `],h([v({attribute:!1})],z.prototype,"service",2),h([v({type:Boolean})],z.prototype,"visible",2),z=h([k("simplecmp-service-toggle")],z);var L=class extends b{constructor(){super(...arguments);this.purpose="";this.services=[];this._expanded=!1;this._onMasterChange=t=>{let n=t.target.checked;if(this.manager!==void 0){for(let i of this.services)i.required!==!0&&this.manager.updateConsent(i.name,n);this._emit("purpose-toggle",{purpose:this.purpose,value:n})}};this._toggleExpanded=t=>{t.preventDefault(),this._expanded=!this._expanded}}render(){if(this.manager===void 0)return d;let t=this._computeStatus(),n=this._tString(["!","purposes",this.purpose,"title?"])||A(this.purpose),i=this._tString(["!","purposes",this.purpose,"description"]),o=`simplecmp-purpose-${this.purpose}`;return u`
      <div class="header">
        <input
          type="checkbox"
          id=${o}
          .checked=${t.allEnabled||!t.allDisabled&&!t.onlyRequiredEnabled}
          .indeterminate=${!t.allEnabled&&!t.allDisabled}
          ?disabled=${t.allRequired}
          @change=${this._onMasterChange}
        />
        <div class="meta">
          <label for=${o}>
            <span class="title">${n}</span>
          </label>
          ${i?u`<p class="description">${i}</p>`:d}
        </div>
      </div>

      ${this.services.length>0?u`
            <button
              type="button"
              class="toggle-services"
              aria-expanded=${this._expanded?"true":"false"}
              @click=${this._toggleExpanded}
            >
              ${this._expanded?"\u25B4":"\u25BE"} ${this.services.length}
              ${this._t(["purposeItem",this.services.length>1?"services":"service"])}
            </button>
            <ul class="services" ?hidden=${!this._expanded}>
              ${this.services.map(s=>u`
                  <li>
                    <simplecmp-service-toggle
                      .config=${this.config}
                      .manager=${this.manager}
                      .service=${s}
                      .visible=${this._expanded}
                    ></simplecmp-service-toggle>
                  </li>
                `)}
            </ul>
          `:d}
    `}_computeStatus(){let t=this.manager?.consents??{},n={allEnabled:!0,allDisabled:!0,onlyRequiredEnabled:!0,allRequired:!0};for(let i of this.services){let o=i.required===!0;o||(n.allRequired=!1),t[i.name]?(o||(n.onlyRequiredEnabled=!1),n.allDisabled=!1):o||(n.allEnabled=!1)}return n.allDisabled&&(n.onlyRequiredEnabled=!1),n}_tString(t){let n=this._t(t);return typeof n=="string"?n:Array.isArray(n)?n.map(i=>typeof i=="string"?i:"").join(""):""}};L.styles=[ge,y`
      :host {
        display: block;
        border: 1px solid var(--simplecmp-color-border);
        border-radius: var(--simplecmp-radius);
        padding: var(--simplecmp-spacing);
        margin-bottom: var(--simplecmp-spacing-sm);
      }

      .header {
        display: flex;
        align-items: flex-start;
        gap: var(--simplecmp-spacing-sm);
      }

      input[type='checkbox'] {
        margin-top: 0.25rem;
        accent-color: var(--simplecmp-color-primary);
      }

      .title {
        font-weight: 500;
      }

      .description {
        margin: 0.25rem 0 0 0;
        font-size: var(--simplecmp-font-size-sm);
        color: var(--simplecmp-color-text-muted);
      }

      .toggle-services {
        margin-top: var(--simplecmp-spacing-sm);
        background: none;
        border: none;
        padding: 0;
        font: inherit;
        font-size: var(--simplecmp-font-size-sm);
        color: var(--simplecmp-color-primary);
        cursor: pointer;
      }

      .services {
        margin-top: var(--simplecmp-spacing-sm);
        padding-left: var(--simplecmp-spacing-lg);
        border-left: 2px solid var(--simplecmp-color-border);
      }

      .services[hidden] {
        display: none;
      }
    `],h([v({type:String})],L.prototype,"purpose",2),h([v({attribute:!1})],L.prototype,"services",2),h([Oe()],L.prototype,"_expanded",2),L=h([k("simplecmp-purpose-group")],L);function Yi(r,e){if(r!==void 0){if(typeof r=="string")return r;if(typeof r=="object")return r[e]??r.default}}var S=class extends b{constructor(){super(...arguments);this.open=!1;this.testing=!1;this._onKeydown=t=>{if(t.key!=="Tab")return;let n=this._collectFocusable();if(n.length===0)return;let i=n[0],o=n[n.length-1];if(i===void 0||o===void 0)return;let s=this._deepActiveElement();!t.shiftKey&&s===o?(t.preventDefault(),i.focus()):t.shiftKey&&s===i&&(t.preventDefault(),o.focus())};this._onCancel=t=>{this.config?.mustConsent===!0&&t.preventDefault()};this._onClose=()=>{this.open=!1,this._emit("modal-close")};this._onCloseClick=()=>{this.config?.mustConsent!==!0&&(this.open=!1)};this._onBackdropClick=t=>{t.target===this._dialog&&this.config?.mustConsent!==!0&&(this.open=!1)};this._onAcceptAll=()=>{this.manager!==void 0&&(this.manager.changeAll(!0),this.manager.saveAndApplyConsents("accept"),this._emit("accept"),this.open=!1)};this._onDecline=()=>{this.manager!==void 0&&(this.manager.changeAll(!1),this.manager.saveAndApplyConsents("decline"),this._emit("decline"),this.open=!1)};this._onSave=()=>{this.manager!==void 0&&(this.manager.saveAndApplyConsents("save"),this._emit("save"),this.open=!1)}}updated(t){if(super.updated?.(t),t.has("open")){let n=this._dialog;if(n===void 0)return;this.open&&!n.open?n.showModal():!this.open&&n.open&&n.close()}}render(){let t=this.config,n=this.manager;return t===void 0||n===void 0?d:u`
      <dialog
        aria-labelledby="simplecmp-modal-title"
        @cancel=${this._onCancel}
        @close=${this._onClose}
        @click=${this._onBackdropClick}
        @keydown=${this._onKeydown}
      >
        ${this._renderHeader(t)}
        <div class="body">${this._renderBody(t)}</div>
        ${this._renderFooter(t,n)}
      </dialog>
    `}_renderHeader(t){let n=this._activeLang(),i=this._resolvePolicyUrl(t.privacyPolicy,["privacyPolicyUrl"],n),o=this._resolvePolicyUrl(t.imprint,["imprintUrl"],n),s=t.htmlTexts===!0,a=this._t(["consentModal","description"]),c=t.mustConsent!==!0;return u`
      <div class="header">
        ${c?u`<button
              type="button"
              class="close"
              aria-label=${this._t(["close"])}
              @click=${this._onCloseClick}
            >
              ×
            </button>`:d}
        <h1 id="simplecmp-modal-title">${this._t(["consentModal","title"])}</h1>
        <p class="description">
          ${s?Zi(a):a}
        </p>
        ${this._renderPolicyLinks(i,o)}
      </div>
    `}_renderBody(t){if(t.groupByPurpose!==!1){let i=this._collectPurposes(),o=t.purposeOrder??[],s=Array.from(i.keys()).sort((a,c)=>o.indexOf(a)-o.indexOf(c));return u`
        <div class="purposes">
          ${s.map(a=>u`
              <simplecmp-purpose-group
                .config=${this.config}
                .manager=${this.manager}
                .purpose=${a}
                .services=${i.get(a)??[]}
              ></simplecmp-purpose-group>
            `)}
        </div>
      `}return u`
      <ul class="services">
        ${t.services.map(i=>u`
            <li>
              <simplecmp-service-toggle
                .config=${this.config}
                .manager=${this.manager}
                .service=${i}
              ></simplecmp-service-toggle>
            </li>
          `)}
      </ul>
    `}_renderFooter(t,n){let i=t.hideDeclineAll!==!0,o=t.acceptAll===!0,s=n.confirmed?this._t(["save"]):this._t(["acceptSelected"]);return u`
      <div class="footer">
        ${i?u`<button type="button" class="action decline" @click=${this._onDecline}>
              ${this._t(["decline"])}
            </button>`:d}
        <button type="button" class="action save" @click=${this._onSave}>
          ${s}
        </button>
        ${o?u`<button type="button" class="action accept-all" @click=${this._onAcceptAll}>
              ${this._t(["acceptAll"])}
            </button>`:d}
      </div>
    `}_renderPolicyLinks(t,n){let i=t!==void 0&&ve(t)?t:void 0,o=n!==void 0&&ve(n)?n:void 0;return i===void 0&&o===void 0?d:u`
      <p class="policy-links">
        ${i?u`<a href=${i} target="_blank" rel="noopener"
              >${this._t(["privacyPolicy","name"])}</a
            >`:d}
        ${i&&o?" \xB7 ":d}
        ${o?u`<a href=${o} target="_blank" rel="noopener"
              >${this._imprintLinkText()}</a
            >`:d}
      </p>
    `}_activeLang(){return this.config?.lang??document.documentElement.lang??"en"}_collectFocusable(){let t=this._dialog;if(t===void 0)return[];let n=[],i='button:not([disabled]), a[href], input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',o=s=>{if(s.matches(i)&&this._isVisible(s)&&n.push(s),s.shadowRoot!==null)for(let a of s.shadowRoot.children)o(a);for(let a of s.children)o(a)};for(let s of t.children)o(s);return n}_deepActiveElement(){let t=document.activeElement;for(;t?.shadowRoot?.activeElement;)t=t.shadowRoot.activeElement;return t}_isVisible(t){let n=t;return typeof n.checkVisibility=="function"?n.checkVisibility():t.offsetParent!==null}_collectPurposes(){let t=new Map;for(let n of this.config?.services??[])for(let i of n.purposes??[]){let o=t.get(i)??[];o.push(n),t.set(i,o)}return t}_resolvePolicyUrl(t,n,i){let o=Yi(t,i);if(o!==void 0)return o;let s=this._tString(["!",...n]);return s===""?void 0:s}_imprintLinkText(){return this._tString(["!","consentNotice","imprint","name"])||this._tString(["!","imprint","name"])||"Imprint"}_tString(t){let n=this._t(t);return typeof n=="string"?n:Array.isArray(n)?n.map(i=>typeof i=="string"?i:"").join(""):""}};S.styles=[w,y`
      :host {
        display: contents;
      }

      dialog {
        max-width: 40rem;
        width: 90%;
        border: 1px solid var(--simplecmp-color-border);
        border-radius: var(--simplecmp-radius);
        padding: 0;
        background: var(--simplecmp-color-bg);
        color: var(--simplecmp-color-text);
        font-family: var(--simplecmp-font-family);
        font-size: var(--simplecmp-font-size);
      }

      dialog::backdrop {
        background: rgba(0, 0, 0, 0.4);
      }

      .header,
      .body,
      .footer {
        padding: var(--simplecmp-spacing-lg);
      }

      .header {
        border-bottom: 1px solid var(--simplecmp-color-border);
        position: relative;
      }

      h1 {
        margin: 0 0 var(--simplecmp-spacing) 0;
        font-family: var(--simplecmp-font-family-heading);
        font-size: var(--simplecmp-font-size-heading);
      }

      .description {
        margin: 0 0 var(--simplecmp-spacing) 0;
      }

      .policy-links {
        margin: 0;
        font-size: var(--simplecmp-font-size-sm);
        color: var(--simplecmp-color-text-muted);
      }

      .policy-links a {
        color: var(--simplecmp-color-text-muted);
      }

      .close {
        position: absolute;
        top: var(--simplecmp-spacing);
        right: var(--simplecmp-spacing);
        background: none;
        border: none;
        font-size: 1.25rem;
        line-height: 1;
        cursor: pointer;
        color: var(--simplecmp-color-text-muted);
      }

      .footer {
        border-top: 1px solid var(--simplecmp-color-border);
        display: flex;
        gap: var(--simplecmp-spacing-sm);
        flex-wrap: wrap;
        justify-content: flex-end;
      }

      button.action {
        font: inherit;
        border: 1px solid transparent;
        border-radius: var(--simplecmp-radius);
        padding: var(--simplecmp-spacing-sm) var(--simplecmp-spacing);
        cursor: pointer;
      }

      button.accept-all,
      button.save {
        background: var(--simplecmp-color-primary);
        color: white;
      }

      button.accept-all:hover,
      button.save:hover {
        background: var(--simplecmp-color-primary-hover);
      }

      button.decline {
        background: transparent;
        color: var(--simplecmp-color-danger);
        border-color: var(--simplecmp-color-danger);
      }

      ul.services {
        list-style: none;
        padding: 0;
        margin: 0;
      }
    `],h([v({type:Boolean,reflect:!0})],S.prototype,"open",2),h([v({type:Boolean})],S.prototype,"testing",2),h([Le("dialog")],S.prototype,"_dialog",2),S=h([k("simplecmp-modal")],S);function Zi(r){return typeof r=="string"?X(r):Array.isArray(r)?r.map(e=>typeof e=="string"?X(e):e):r}var $=class extends b{constructor(){super(...arguments);this.position="bottom-right";this._onClick=t=>{t.preventDefault(),this._emit("trigger-click")}}connectedCallback(){super.connectedCallback(),this.setAttribute("position",this.position)}render(){let t=this._resolveLabel();return u`
      <button type="button" aria-label=${t} title=${t} @click=${this._onClick}>
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
          <path
            fill="currentColor"
            d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10c0-.34-.02-.68-.05-1.01-.71.93-1.83 1.51-3.07 1.51-2.21 0-4-1.79-4-4 0-.34.04-.68.13-1.01-1.65.32-3.13-1.04-3.13-2.49 0-.79.36-1.5.93-1.96A9.95 9.95 0 0 0 12 2zm-1 5h2v2h-2zm-3 4h2v2H8zm6 0h2v2h-2zm-2 4h2v2h-2z"
          />
        </svg>
      </button>
    `}_resolveLabel(){if(this.label!==void 0&&this.label!=="")return this.label;if(this.config!==void 0){let t=this._tString(["!","floatingTrigger","label"]);if(t!=="")return t;if(be(this.config.region,this.config.regimes,this.config.regimeDefault)==="opt-out"){let i=this._tString(["!","doNotSell"]);if(i!=="")return i}}return"Cookie settings"}_tString(t){let n=this._t(t);return typeof n=="string"?n:Array.isArray(n)?n.map(i=>typeof i=="string"?i:"").join(""):""}};$.styles=[w,y`
      :host {
        display: contents;
      }

      button {
        position: fixed;
        z-index: var(--simplecmp-z-index);
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: var(--simplecmp-color-primary);
        color: white;
        border: none;
        box-shadow: var(--simplecmp-shadow);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
      }

      button:hover {
        background: var(--simplecmp-color-primary-hover);
      }

      :host([position='bottom-right']) button {
        right: var(--simplecmp-spacing);
        bottom: var(--simplecmp-spacing);
      }

      :host([position='bottom-left']) button {
        left: var(--simplecmp-spacing);
        bottom: var(--simplecmp-spacing);
      }

      :host([position='top-right']) button {
        right: var(--simplecmp-spacing);
        top: var(--simplecmp-spacing);
      }

      :host([position='top-left']) button {
        left: var(--simplecmp-spacing);
        top: var(--simplecmp-spacing);
      }

      svg {
        width: 1.25rem;
        height: 1.25rem;
      }
    `],h([v({type:String})],$.prototype,"position",2),h([v({type:String})],$.prototype,"label",2),$=h([k("simplecmp-trigger")],$);function yn(r){let e=re(r),t=r.domMode==="light",n;r.noNotice!==!0&&e.bannerMode!=="none"&&(!e.confirmed||r.testing===!0)&&(n=new D,n.config=r,n.manager=e,r.testing===!0&&(n.testing=!0),t&&n.setAttribute("mode","light"),document.body.appendChild(n));let o=new S;o.config=r,o.manager=e,r.testing===!0&&(o.testing=!0),t&&o.setAttribute("mode","light"),document.body.appendChild(o),r.mustConsent===!0&&!e.confirmed&&(o.open=!0);let s=()=>{o.open=!0};document.addEventListener("simplecmp:configure",s);let a={update(f,p){p==="saveConsents"&&n!==void 0&&(n.remove(),n=void 0)}};e.watch(a);let c,l;return r.floatingTrigger&&(c=new $,c.config=r,typeof r.floatingTrigger=="object"&&(r.floatingTrigger.position!==void 0&&(c.position=r.floatingTrigger.position),r.floatingTrigger.label!==void 0&&(c.label=r.floatingTrigger.label)),t&&c.setAttribute("mode","light"),document.body.appendChild(c),l=()=>{o.open=!0},c.addEventListener("simplecmp:trigger-click",l)),e.applyConsents(),{show(){o.open=!0},hide(){o.open=!1},destroy(){e.unwatch(a),document.removeEventListener("simplecmp:configure",s),n?.remove(),o.remove(),c!==void 0&&l!==void 0&&(c.removeEventListener("simplecmp:trigger-click",l),c.remove())},manager:e}}var bn=`:where(
    simplecmp-banner,
    simplecmp-modal,
    simplecmp-purpose-group,
    simplecmp-service-toggle,
    simplecmp-trigger,
    simplecmp-policy-links,
    simplecmp-contextual-notice
  ) {
  /* Colors */
  --simplecmp-color-primary: var(--bs-primary, #0d6efd);
  --simplecmp-color-primary-hover: var(--bs-primary-bg-subtle, #084298);
  --simplecmp-color-secondary: var(--bs-secondary, #6c757d);
  --simplecmp-color-danger: var(--bs-danger, #dc3545);
  --simplecmp-color-bg: var(--bs-body-bg, #ffffff);
  --simplecmp-color-bg-alt: var(--bs-tertiary-bg, #f8f9fa);
  --simplecmp-color-border: var(--bs-border-color, #dee2e6);
  --simplecmp-color-text: var(--bs-body-color, #212529);
  --simplecmp-color-text-muted: var(--bs-secondary-color, #6c757d);

  /* Geometry */
  --simplecmp-radius: var(--bs-border-radius, 0.375rem);
  --simplecmp-spacing: var(--bs-spacer, 1rem);
  --simplecmp-spacing-sm: 0.5rem;
  --simplecmp-spacing-lg: 1.5rem;

  /* Typography */
  --simplecmp-font-family: var(--bs-body-font-family, system-ui);
  --simplecmp-font-size: var(--bs-body-font-size, 1rem);
  --simplecmp-font-size-sm: 0.875rem;
  --simplecmp-line-height: var(--bs-body-line-height, 1.5);

  /* Effects */
  --simplecmp-shadow: var(--bs-box-shadow, 0 0.5rem 1rem rgba(0, 0, 0, 0.15));
}`;var kn=`:where(
    simplecmp-banner,
    simplecmp-modal,
    simplecmp-purpose-group,
    simplecmp-service-toggle,
    simplecmp-trigger,
    simplecmp-policy-links,
    simplecmp-contextual-notice
  ) {
  /* Colors \u2014 bind to Bulma's semantic palette. Hover falls back to
     a literal (Bulma offers a generated --bulma-primary-base for
     hover/dark adjustments but it's not always present on every
     theme; the literal #1e6cbf is Bulma's default primary darkened
     by ~12% for a sensible hover treatment). */
  --simplecmp-color-primary: var(--bulma-primary, #485fc7);
  --simplecmp-color-primary-hover: var(--bulma-primary-30-invert, var(--bulma-primary-dark, #1e6cbf));
  --simplecmp-color-secondary: var(--bulma-text-weak, #6c757d);
  --simplecmp-color-danger: var(--bulma-danger, #f14668);
  --simplecmp-color-bg: var(--bulma-scheme-main, #ffffff);
  --simplecmp-color-bg-alt: var(--bulma-scheme-main-bis, #fafafa);
  --simplecmp-color-border: var(--bulma-border, #ededed);
  --simplecmp-color-text: var(--bulma-text-strong, #2c2c2c);
  --simplecmp-color-text-muted: var(--bulma-text-weak, #6e6e6e);

  /* Geometry. Bulma exposes --bulma-radius for normal corners,
     and -small / -large for the rest. We pick the medium for
     buttons/cards. Spacing uses --bulma-block-spacing which Bulma
     1.0 sets to 1.5rem (closer to card-internal feel than the
     smaller 0.5rem element padding). */
  --simplecmp-radius: var(--bulma-radius, 4px);
  --simplecmp-spacing: var(--bulma-block-spacing, 1.5rem);
  --simplecmp-spacing-sm: 0.5rem;
  --simplecmp-spacing-lg: 2rem;

  /* Typography */
  --simplecmp-font-family: var(--bulma-family-primary, system-ui);
  --simplecmp-font-size: var(--bulma-size-normal, 1rem);
  --simplecmp-font-size-sm: var(--bulma-size-small, 0.875rem);
  --simplecmp-line-height: var(--bulma-body-line-height, 1.5);

  /* Effects */
  --simplecmp-shadow: var(--bulma-shadow, 0 0.5em 1em -0.125em rgba(10, 10, 10, 0.1));
}
`;var wn=`:where(
    simplecmp-banner,
    simplecmp-modal,
    simplecmp-purpose-group,
    simplecmp-service-toggle,
    simplecmp-trigger,
    simplecmp-policy-links,
    simplecmp-contextual-notice
  ) {
  /* Colors. Pico exposes a primary palette via --pico-primary and
     --pico-primary-hover for hover states; both are stable in v2.
     Background pair: --pico-background-color is the body / card,
     --pico-card-background-color is what cards specifically use
     (slightly elevated). Text follows the same pattern with
     --pico-color and --pico-muted-color. */
  --simplecmp-color-primary: var(--pico-primary, #0172ad);
  --simplecmp-color-primary-hover: var(--pico-primary-hover, #015887);
  --simplecmp-color-secondary: var(--pico-secondary, #5d6b89);
  --simplecmp-color-danger: var(--pico-del-color, #c62828);
  --simplecmp-color-bg: var(--pico-card-background-color, var(--pico-background-color, #ffffff));
  --simplecmp-color-bg-alt: var(--pico-card-sectioning-background-color, #f9f9f9);
  --simplecmp-color-border: var(--pico-muted-border-color, #e1e6eb);
  --simplecmp-color-text: var(--pico-color, #373c44);
  --simplecmp-color-text-muted: var(--pico-muted-color, #646b79);

  /* Geometry */
  --simplecmp-radius: var(--pico-border-radius, 0.25rem);
  --simplecmp-spacing: var(--pico-spacing, 1rem);
  --simplecmp-spacing-sm: calc(var(--pico-spacing, 1rem) * 0.5);
  --simplecmp-spacing-lg: calc(var(--pico-spacing, 1rem) * 1.5);

  /* Typography */
  --simplecmp-font-family: var(--pico-font-family, system-ui);
  --simplecmp-font-size: var(--pico-font-size, 1rem);
  --simplecmp-font-size-sm: calc(var(--pico-font-size, 1rem) * 0.875);
  --simplecmp-line-height: var(--pico-line-height, 1.5);

  /* Effects */
  --simplecmp-shadow: var(--pico-card-box-shadow, 0 0.125rem 1rem rgba(0, 0, 0, 0.04));
}
`;var Sn=`:where(
    simplecmp-banner,
    simplecmp-modal,
    simplecmp-purpose-group,
    simplecmp-service-toggle,
    simplecmp-trigger,
    simplecmp-policy-links,
    simplecmp-contextual-notice
  ) {
  /* Colors \u2014 semantic first, Tailwind palette fallback chain. The
     semantic names (--color-primary, --color-background, \u2026) follow
     the shadcn/ui convention which is the de-facto standard for
     Tailwind 4 semantic tokens. Sites that don't define them get
     the palette default (Tailwind's blue/slate). */
  --simplecmp-color-primary: var(--color-primary, var(--color-blue-600, #2563eb));
  --simplecmp-color-primary-hover: var(--color-primary-hover, var(--color-blue-700, #1d4ed8));
  --simplecmp-color-secondary: var(--color-secondary, var(--color-slate-500, #64748b));
  --simplecmp-color-danger: var(--color-destructive, var(--color-red-600, #dc2626));
  --simplecmp-color-bg: var(--color-background, var(--color-white, #ffffff));
  --simplecmp-color-bg-alt: var(--color-muted, var(--color-slate-50, #f8fafc));
  --simplecmp-color-border: var(--color-border, var(--color-slate-200, #e2e8f0));
  --simplecmp-color-text: var(--color-foreground, var(--color-slate-900, #0f172a));
  --simplecmp-color-text-muted: var(--color-muted-foreground, var(--color-slate-500, #64748b));

  /* Geometry. Tailwind 4 spacing is computed: gap-4 \u2192 calc(--spacing * 4).
     We pin --simplecmp-spacing to ~1rem (the visual rhythm the banner
     was designed for) by multiplying the base unit by 4 \u2014 which lands
     at 1rem under Tailwind's default 0.25rem base. Sites that scale
     --spacing up/down keep the banner consistent with their rhythm. */
  --simplecmp-radius: var(--radius-md, 0.375rem);
  --simplecmp-spacing: calc(var(--spacing, 0.25rem) * 4);
  --simplecmp-spacing-sm: calc(var(--spacing, 0.25rem) * 2);
  --simplecmp-spacing-lg: calc(var(--spacing, 0.25rem) * 6);

  /* Typography */
  --simplecmp-font-family: var(--font-sans, system-ui, -apple-system, sans-serif);
  --simplecmp-font-size: var(--text-base, 1rem);
  --simplecmp-font-size-sm: var(--text-sm, 0.875rem);
  --simplecmp-line-height: var(--leading-normal, 1.5);

  /* Effects */
  --simplecmp-shadow: var(--shadow-lg, 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1));
}
`;var ut="data-simplecmp-theme",_n={bootstrap5:bn,tailwind4:Sn,bulma:kn,pico:wn};function Cn(r){if(typeof document>"u")return;let e=document.querySelector(`style[${ut}]`);if(r===void 0||r==="default"){e?.remove();return}let t=_n[r];if(t===void 0){console.warn(`SimpleCMP: theme "${r}" has no adapter; ignoring. Known themes: ${Object.keys(_n).join(", ")}, 'default'.`);return}if(e?.getAttribute(ut)===r)return;let n=e instanceof HTMLStyleElement?e:document.createElement("style");n.setAttribute(ut,r),n.textContent=t,n.isConnected||document.head.appendChild(n)}function er(r){return _t(r)}function tr(r){return Ct(r)}function nr(r){return Ue(r)}E(Qe,W({en:Ht}));var ir="0.4.1",Y=null,ft=0;function xn(r){typeof requestIdleCallback=="function"?requestIdleCallback(r,{timeout:2e3}):setTimeout(r,200)}var q=null,F=null;function rr(r){fr(r),mr(r);let e=++ft,t=pr(),n=t?{...r,testing:!0}:r;t&&ur(),Y!==null&&(Y.destroy(),Y=null),q!==null&&(q(),q=null),F!==null&&(F(),F=null),Cn(r.theme);let i=re(n);n.record&&(n.deferRecorder?xn(()=>{e===ft&&An(n)}):An(n)),n.interceptRuntime&&(q=or(n,i)),n.consentMode&&(F=Je(n.consentMode,i,n.services));let o=null,s=!1,a=[],c=()=>{if(!(s||e!==ft)){o=yn(n);for(let m of a)m==="show"?o.show():o.hide();a.length=0}},f=n.deferRender===!0&&!t?()=>xn(c):c,p=null;return typeof document<"u"&&document.body!==null?f():typeof document<"u"&&(p=f,document.addEventListener("DOMContentLoaded",p,{once:!0})),Y={show:()=>{o!==null?o.show():a.push("show")},hide:()=>{o!==null?o.hide():a.push("hide")},manager:i,destroy:()=>{s=!0,q!==null&&(q(),q=null),F!==null&&(F(),F=null),p!==null&&typeof document<"u"&&(document.removeEventListener("DOMContentLoaded",p),p=null),o?.destroy(),o=null,a.length=0}},Y}function or(r,e){let t=typeof r.interceptRuntime=="object"&&r.interceptRuntime!==null?r.interceptRuntime:{},n=Qt(r.services,{blockAllUnknown:t.universalBlock===!0}),i=t.onBlock;return Jt({matcher:n,consentChecker:o=>e.getConsent(o),sameOriginHosts:t.sameOriginHosts,onBlock:o=>{N!==null&&N.recordSyntheticDetection({kind:Gt(o.mechanism),identifier:o.url,origin:Kt(o.url)}),i?.(o)}})}function sr(){Y?.show()}var N=null;function An(r){N&&(N.stop(),N=null),N=Vt(r),N.start()}function ar(){return N??void 0}var cr=Bt,lr=re,dr=ne;function pr(){if(typeof window>"u"||typeof URLSearchParams>"u")return!1;try{if(new URLSearchParams(window.location.search).get("simplecmp_audit")==="1")return!0;let e=window.location.hash;return e.length>1&&new URLSearchParams(e.slice(1)).get("simplecmp_audit")==="1"}catch{return!1}}function ur(){typeof window>"u"||requestAnimationFrame(()=>{requestAnimationFrame(()=>{requestAnimationFrame(()=>{let r=Ue();try{window.parent.postMessage({type:"simplecmp-audit-from-fe",results:r,location:{href:window.location.href,host:window.location.host}},"*")}catch{}})})})}function fr(r){r.cmsBridgeUrl&&!r.record&&console.warn("SimpleCMP: `cmsBridgeUrl` is set but `record` is not enabled. The CMS bridge listens to recorder detections \u2014 without the recorder running, no webhooks will ever fire. Set `record: true` or remove `cmsBridgeUrl`.")}function mr(r){r.hideDeclineAll&&console.warn('SimpleCMP: `hideDeclineAll: true` hides the "Decline all" button on the first banner level. This is incompatible with German consent requirements (BGH "Cookie II", BGH I ZR 7/16; DSK 2022). Keep the decline option equally prominent or expect compliance issues.')}return Dn(hr);})();
/*! Bundled license information:

@lit/reactive-element/css-tag.js:
  (**
   * @license
   * Copyright 2019 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/reactive-element.js:
lit-html/lit-html.js:
lit-element/lit-element.js:
@lit/reactive-element/decorators/custom-element.js:
@lit/reactive-element/decorators/property.js:
@lit/reactive-element/decorators/state.js:
@lit/reactive-element/decorators/event-options.js:
@lit/reactive-element/decorators/base.js:
@lit/reactive-element/decorators/query.js:
@lit/reactive-element/decorators/query-all.js:
@lit/reactive-element/decorators/query-async.js:
@lit/reactive-element/decorators/query-assigned-nodes.js:
lit-html/directive.js:
lit-html/directives/unsafe-html.js:
  (**
   * @license
   * Copyright 2017 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

lit-html/is-server.js:
  (**
   * @license
   * Copyright 2022 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

@lit/reactive-element/decorators/query-assigned-elements.js:
  (**
   * @license
   * Copyright 2021 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)

lit-html/directives/if-defined.js:
  (**
   * @license
   * Copyright 2018 Google LLC
   * SPDX-License-Identifier: BSD-3-Clause
   *)
*/
//# sourceMappingURL=simplecmp.core.global.js.map