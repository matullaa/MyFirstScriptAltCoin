try {
    sap.ui.getCore().loadLibrary("sap.ui.commons");
} catch (e) {
    alert("This test page requires the library 'sap.ui.commons' which is not available.");
    throw(e);
}

window.sLorem = "Lorem ipsum dolor sit amet, consetetur " +
    "sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et " +
    "dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam " +
    "et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea " +
    "takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit " +
    "amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor " +
    "invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. " +
    "At vero eos et accusam et justo duo dolores et ea rebum. Stet clita " +
    "iriure dolor in hendrerit in vulputate velit esse molestie consequat, " +
    "vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan " +
    "et iusto odio dignissim qui blandit praesent luptatum zzril delenit " +
    "augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit " +
    "augue duis dolore te feugait nulla facilisi.\n\n";

sap.ui.core.Control.extend("SearchFieldPlaceHolder", {
    metadata: {
        events: {
            "search": {}
        }
    },

    renderer: function (rm, ctrl) {
        rm.write("<div");
        rm.writeControlData(ctrl);
        rm.writeAttribute("class", "SearchFieldPlaceHolder");
        rm.writeAttribute("tabindex", "0");
        rm.write(">Placeholder for a SearchField Control</div>");
    },

    onclick: function (evt) {
        this.fireSearch();
    }
});

sap.ui.core.Control.extend("CurtainContent", {
    metadata: {
        properties: {
            "text": "string",
            "headerHidden": "boolean"
        },
        aggregations: {
            "content": {type: "sap.ui.core.Control", multiple: true}
        }
    },

    renderer: function (rm, ctrl) {
        rm.write("<div");
        rm.addClass("CurtainContent");
        rm.writeClasses();
        rm.writeControlData(ctrl);
        rm.write("><header");
        rm.addClass("_sapUiUfdShellSubHdr");
        rm.writeClasses();
        rm.write(">");
        rm.writeEscaped(ctrl.getText());
        rm.write("</header><div>");
        var aContent = ctrl.getContent();
        for (var i = 0; i < aContent.length; i++) {
            rm.renderControl(aContent[i]);
        }
        rm.write("</div></div>");
    },

    setHeaderHidden: function (bHidden) {
        this.setProperty("headerHidden", !!bHidden, true);
        this.$().toggleClass("CurtainContentHeaderHidden", !!bHidden);
    }
});

jQuery.sap.require("sap.ui.core.IconPool");
//jQuery.sap.require("jQuery.sap.resources");

jQuery(function () {
    jQuery("head").append("<link type='text/css' rel='stylesheet' href='../styles/ShellTestPage.css'>");
});