(function(){function e(a){"activeLine"in a.state&&(a.removeLineClass(a.state.activeLine,"wrap",f),a.removeLineClass(a.state.activeLine,"background",g))}function d(a){var b=a.getLineHandle(a.getCursor().line);a.state.activeLine!=b&&(e(a),a.addLineClass(b,"wrap",f),a.addLineClass(b,"background",g),a.state.activeLine=b)}var f="CodeMirror-activeline",g="CodeMirror-activeline-background";CodeMirror.defineOption("styleActiveLine",!1,function(a,b,c){c=c&&c!=CodeMirror.Init;b&&!c?(d(a),a.on("cursorActivity", d)):!b&&c&&(a.off("cursorActivity",d),e(a),delete a.state.activeLine)})})();