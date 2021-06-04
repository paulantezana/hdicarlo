let pValidator,map,marker,customerPValidator,exhibitorGlobalMap,exhibitorGlobalMarker,exhibitorState={modalType:"create",modalName:"exhibitorModalForm",loading:!1,slimCustomerId:null,slimGeoLocation:null},customerState={modalType:"create",modalName:"customerModalForm",loading:!1};function exhibitorSetLoading(e){exhibitorState.loading=e;let t=document.querySelectorAll(".jsExhibitorAction"),o=document.getElementById("exhibitorFormSubmit");exhibitorState.loading?(o&&(o.setAttribute("disabled","disabled"),o.classList.add("loading")),t&&t.forEach(e=>{e.setAttribute("disabled","disabled")})):(o&&(o.removeAttribute("disabled"),o.classList.remove("loading")),t&&t.forEach(e=>{e.removeAttribute("disabled")}))}function exhibitorList(e=1,t=10,o=""){let i=document.getElementById("exhibitorTable");i&&(SnFreeze.freeze({selector:"#exhibitorTable"}),RequestApi.fetch(`/admin/exhibitor/table?limit=${t}&page=${e}&search=${o}`,{method:"GET"}).then(e=>{e.success?(i.innerHTML=e.view,SnDropdown()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{SnFreeze.unFreeze("#exhibitorTable")}))}function exhibitorClearForm(){let e=document.getElementById("exhibitorForm"),t=document.getElementById("exhibitorCode");e&&e.reset(),t&&setTimeout(()=>{t.focus()},500),exhibitorState.slimCustomerId.set(0),exhibitorState.slimGeoLocation.set(0),pValidator.reset(),geoGetCurrentPosition().then(e=>{drawGoogleMap(e)}).catch(e=>toastr.error(e,"Algo salió mal!!"))}function exhibitorSubmit(e){if(e.preventDefault(),!pValidator.validate())return;exhibitorSetLoading(!0);let t={};t.code=document.getElementById("exhibitorCode").value,t.sizeId=document.getElementById("exhibitorSizeId").value,t.customerId=document.getElementById("exhibitorCustomerId").value,t.geoLocationId=document.getElementById("exhibitorGeoLocationId").value,t.address=document.getElementById("exhibitorAddress").value,t.latitude=document.getElementById("exhibitorLatitude").value,t.longitude=document.getElementById("exhibitorLongitude").value,"update"===exhibitorState.modalType&&(t.exhibitorId=document.getElementById("exhibitorId").value||0),RequestApi.fetch("/admin/exhibitor/"+exhibitorState.modalType,{method:"POST",body:t}).then(e=>{e.success?(SnModal.close(exhibitorState.modalName),SnMessage.success({content:e.message}),exhibitorList()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{exhibitorSetLoading(!1)})}function exhibitorDelete(e,t=""){SnModal.confirm({title:"¿Estás seguro de eliminar este registro?",content:t,okText:"Si",okType:"error",cancelText:"No",onOk(){exhibitorSetLoading(!0),RequestApi.fetch("/admin/exhibitor/delete",{method:"POST",body:{exhibitorId:e||0}}).then(e=>{e.success?(SnMessage.success({content:e.message}),exhibitorList()):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{exhibitorSetLoading(!1)})}})}function exhibitorShowModalCreate(){exhibitorState.modalType="create",exhibitorClearForm(),SnModal.open(exhibitorState.modalName)}function exhibitorShowModalUpdate(e){exhibitorState.modalType="update",exhibitorGetById(e)}function exhibitorGetById(e){exhibitorClearForm(),exhibitorSetLoading(!0),RequestApi.fetch("/admin/exhibitor/id",{method:"POST",body:{exhibitorId:e||0}}).then(e=>{if(e.success){if(document.getElementById("exhibitorId").value=e.result.exhibitor_id,document.getElementById("exhibitorCode").value=e.result.code,document.getElementById("exhibitorSizeId").value=e.result.size_id,e.result.lat_long.length>2){let t=e.result.lat_long.split(","),o=t[0],i=t[1];document.getElementById("exhibitorLatitude").value=o,document.getElementById("exhibitorLongitude").value=i,document.getElementById("exhibitorAddress").value=e.result.address,setPositionMarker({lat:o,lng:i})}exhibitorState.slimCustomerId.setData([{text:e.result.customer_social_reason,value:e.result.customer_id}]),exhibitorState.slimCustomerId.set(e.result.customer_id),exhibitorState.slimGeoLocation.setData([{text:e.result.geo_name,value:e.result.geo_location_id}]),exhibitorState.slimGeoLocation.set(e.result.geo_location_id),SnModal.open(exhibitorState.modalName)}else SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{exhibitorSetLoading(!1)})}function exhibitorToExcel(){let e=document.getElementById("exhibitorCurrentTable");e&&TableToExcel(e.outerHTML,"Usuario","Usuario")}function exhibitorToPrint(){printArea("exhibitorCurrentTable")}function customerSetLoading(e){customerState.loading=e;let t=document.querySelectorAll(".jsCustomerAction"),o=document.getElementById("customerFormSubmit");customerState.loading?(o&&(o.setAttribute("disabled","disabled"),o.classList.add("loading")),t&&t.forEach(e=>{e.setAttribute("disabled","disabled")})):(o&&(o.removeAttribute("disabled"),o.classList.remove("loading")),t&&t.forEach(e=>{e.removeAttribute("disabled")}))}function customerShowModalCreate(){customerState.modalType="create",customerClearForm(),SnModal.open(customerState.modalName)}function customerClearForm(){let e=document.getElementById("customerForm"),t=document.getElementById("customerEmail");e&&t&&(e.reset(),t.focus()),customerPValidator.reset()}function customerSubmit(e){if(e.preventDefault(),!customerPValidator.validate())return;customerSetLoading(!0);let t={};t.identityDocumentId=document.getElementById("customerIdentityDocumentId").value,t.documentNumber=document.getElementById("customerDocumentNumber").value,t.socialReason=document.getElementById("customerSocialReason").value,t.commercialReason=document.getElementById("customerCommercialReason").value,t.fiscalAddress=document.getElementById("customerFiscalAddress").value,t.email=document.getElementById("customerEmail").value,t.telephone=document.getElementById("customerTelephone").value,"update"===customerState.modalType&&(t.customerId=document.getElementById("customerId").value||0),RequestApi.fetch("/admin/customer/"+customerState.modalType,{method:"POST",body:t}).then(e=>{e.success?(SnModal.close(customerState.modalName),SnMessage.success({content:e.message}),exhibitorState.slimCustomerId.setData([{text:t.socialReason,value:e.result}]),exhibitorState.slimCustomerId.set(e.result)):SnModal.error({title:"Algo salió mal",content:e.message})}).finally(e=>{customerSetLoading(!1)})}function initGoogleMaps(){geoGetCurrentPosition().then(e=>{drawGoogleMap(e),drawGoogleMapExhibitorGlobal(e)}).catch(e=>SnMessage.error({content:e}))}function drawGoogleMap(e){map=new google.maps.Map(document.getElementById("googleMap"),{center:e,zoom:17,rotateControl:!1,fullscreenControl:!1,streetViewControl:!1,mapTypeControl:!1}),marker=new google.maps.Marker({position:e,draggable:!0,animation:google.maps.Animation.DROP,map:map}),google.maps.event.addListener(marker,"dragend",function(){let e={lat:marker.position.lat(),lng:marker.position.lng()};document.getElementById("exhibitorLatitude").value=e.lat,document.getElementById("exhibitorLongitude").value=e.lng,(new google.maps.Geocoder).geocode({location:e},function(e,t){"OK"===t?e[0]?$("#exhibitorAddress").val(e[0].formatted_address):SnMessage.error({content:"No se han encontrado resultados: (Google maps)"}):SnMessage.error({content:"Geocoder falló debido a: (Google maps)"})})});let t=document.getElementById("exhibitorAddress"),o=new google.maps.places.Autocomplete(t);o.bindTo("bounds",map),o.setFields(["address_components","geometry","icon","name"]),o.addListener("place_changed",function(){let e=o.getPlace();e.geometry?(e.geometry.viewport?map.fitBounds(e.geometry.viewport):(map.setCenter(e.geometry.location),map.setZoom(17)),marker.setPosition(e.geometry.location),document.getElementById("exhibitorLatitude").value=e.geometry.location.lat(),document.getElementById("exhibitorLongitude").value=e.geometry.location.lng()):SnMessage.error({content:`No hay detalles disponibles para la entrada: '${e.name}': (Google maps)`})})}function setPositionMarker(e){let t=setInterval(()=>{if(map&&marker){clearInterval(t);let o=new google.maps.LatLng(e.lat,e.lng);map.setCenter(o),map.setZoom(17),marker.setPosition(o)}},1e3)}function drawGoogleMapExhibitorGlobal(e){exhibitorGlobalMap=new google.maps.Map(document.getElementById("exhibitorGlobalMap"),{center:e,zoom:17,rotateControl:!1,fullscreenControl:!1,streetViewControl:!1,mapTypeControl:!1}),exhibitorGlobalMarker=new google.maps.Marker({position:e,draggable:!0,animation:google.maps.Animation.DROP,map:exhibitorGlobalMap})}function setPositionMarkerGlobal(e){let t=setInterval(()=>{if(exhibitorGlobalMap&&exhibitorGlobalMarker){clearInterval(t);let o=new google.maps.LatLng(e.lat,e.lng);exhibitorGlobalMap.setCenter(o),exhibitorGlobalMap.setZoom(17),exhibitorGlobalMarker.setPosition(o)}},1e3)}function exhibitorMaintenanceShowModal(){SnModal.open("exhibitorMaintenanceModalForm")}function exhibitorSetPositionMpas(e){let t=e.split(",");if(t.length>0){setPositionMarkerGlobal({lat:t[0],lng:t[1]})}else SnMessage.error({content:"Ubicación mal establecida"})}document.addEventListener("DOMContentLoaded",()=>{pValidator=new Pristine(document.getElementById("exhibitorForm")),customerPValidator=new Pristine(document.getElementById("customerForm")),document.getElementById("searchContent").addEventListener("input",e=>{exhibitorList(1,10,e.target.value)}),exhibitorList(),exhibitorState.slimCustomerId=new SlimSelect({select:"#exhibitorCustomerId",searchingText:"Buscando...",ajax:function(e,t){e.length<2?t("Escriba almenos 2 caracteres"):RequestApi.fetch("/admin/customer/searchBySocialReason",{method:"POST",body:{search:e}}).then(e=>{if(e.success){let o=e.result.map(e=>({text:e.social_reason,value:e.customer_id}));t(o)}else t(!1)}).catch(e=>{t(!1)})}}),exhibitorState.slimGeoLocation=new SlimSelect({select:"#exhibitorGeoLocationId",searchingText:"Buscando...",ajax:function(e,t){e.length<2?t("Escriba almenos 2 caracteres"):RequestApi.fetch("/page/searchLocationLastLevel",{method:"POST",body:{search:e}}).then(e=>{if(e.success){let o=e.result.map(e=>({text:e.geo_name,value:e.geo_location_id}));t(o)}else t(!1)}).catch(e=>{t(!1)})}}),new SlimSelect({select:"#filterCustomerId",searchingText:"Buscando...",ajax:function(e,t){e.length<2?t("Escriba almenos 2 caracteres"):RequestApi.fetch("/admin/customer/searchBySocialReason",{method:"POST",body:{search:e}}).then(e=>{if(e.success){let o=e.result.map(e=>({text:e.social_reason,value:e.customer_id}));t(o)}else t(!1)}).catch(e=>{t(!1)})}})});