webpackJsonp(["app/js/course-manage/batch-create/index"],{"5899c7c7c1283bfb76ec":function(e,t,n){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}function r(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}Object.defineProperty(t,"__esModule",{value:!0});var o=function(){function e(e,t){for(var n=0;n<t.length;n++){var a=t[n];a.enumerable=a.enumerable||!1,a.configurable=!0,"value"in a&&(a.writable=!0),Object.defineProperty(e,a.key,a)}}return function(t,n,a){return n&&e(t.prototype,n),a&&e(t,a),t}}(),i=n("b334fd7e4c5a19234db2"),l=a(i),u=function(){function e(t){r(this,e),this.element=$(t.element),this.uploader=null,this.files=[],this.init()}return o(e,[{key:"init",value:function(){this.initUploader(),this.initEvent()}},{key:"initUploader",value:function(){var e=this,t=this.element;this.uploader=new UploaderSDK({id:t.attr("id"),initUrl:t.data("initUrl"),finishUrl:t.data("finishUrl"),accept:t.data("accept"),process:t.data("process"),ui:"batch",locale:document.documentElement.lang}),this.uploader.on("file.finish",function(t){e.files.push(t)}),this.uploader.on("error",function(e){(0,l.default)("danger",e.message)})}},{key:"initEvent",value:function(){var e=this;$(".js-upload-params").on("change",function(t){e.uploader.setProcess(e.getUploadProcess(t))}),$(".js-batch-create-lesson-btn").on("click",function(t){var n=$(t.currentTarget);n.button("loading"),e.files.map(function(t,a){var r=!1;a+1==e.files.length&&(r=!0),e.createLesson(n,t,r)})}),$('[data-toggle="popover"]').popover({html:!0})}},{key:"getUploadProcess",value:function(e){var t=$(e.currentTarget),n=t.get().reduce(function(e,t){return e[$(t).attr("name")]=$(t).find("option:selected").val(),e},{});return t.find("[name=support_mobile]").length>0&&(n.supportMobile=t.find("[name=support_mobile]").val()),n}},{key:"createLesson",value:function(e,t,n){$.ajax({type:"post",url:e.data("url"),async:!1,data:{fileId:t.id},error:function(e){(0,l.default)("danger",Translator.trans("uploader.status.error"))},complete:function(e){n&&window.location.reload()}})}}]),e}();t.default=u},0:function(e,t,n){"use strict";function a(e){return e&&e.__esModule?e:{default:e}}var r=n("5899c7c7c1283bfb76ec"),o=a(r);new o.default({element:"#batch-uploader"})}});

