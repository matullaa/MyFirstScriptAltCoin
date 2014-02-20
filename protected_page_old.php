<?php
include_once 'include/db_connect.php';
include_once 'include/functions.php';

sec_session_start();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <script id="sap-ui-bootstrap" type="text/javascript"
            src="https://sapui5.netweaver.ondemand.com/resources/sap-ui-core.js"
            data-sap-ui-theme="sap_bluecrystal"
            data-sap-ui-libs="sap.m,sap.me,sap.ui.commons,sap.suite.ui.commons,sap.ui.unified,sap.ui.core"></script>
    <script src="js/ShellTestPage.js" type="text/javascript"></script>
</head>


<body class="sapUiBody"><h3
    style="font-size:20x;left:30%;opacity:0.3;position:absolute;bottom:75px;z-index:9999;text-shadow:none;"></h3>

<form>
    <div id="content"></div>
    <input type="hidden" name="applid" value="ZEASY_POGNON"></form>
<script>

var state, oldState = null;


var configItm = new sap.ui.unified.ShellHeadItem({
    tooltip: "Configuration",
    icon: sap.ui.core.IconPool.getIconURI("menu2"),
    press: function () {
        oShell.setShowPane(!oShell.getShowPane());
        configItm.setSelected(!configItm.getSelected());
    }
});

var curtainConfigItm = new sap.ui.unified.ShellHeadItem({
    tooltip: "Configuration",
    icon: sap.ui.core.IconPool.getIconURI("menu2"),
    showMarker: true,
    press: function () {
        oShell.setShowCurtainPane(!oShell.getShowCurtainPane());
        curtainConfigItm.setSelected(!curtainConfigItm.getSelected());
        curtainConfigItm.setShowMarker(!curtainConfigItm.getShowMarker());
        sap.ui.getCore().byId("CurtainContent").setHeaderHidden(oShell.getShowCurtainPane());
    }
});

var homeItm = new sap.ui.unified.ShellHeadItem({
    tooltip: "Home",
    icon: sap.ui.core.IconPool.getIconURI("home"),
    press: function () {
        setState("HOME");
    }
});

var filterItm = new sap.ui.unified.ShellHeadItem({
    tooltip: "Filter",
    icon: sap.ui.core.IconPool.getIconURI("filter"),
    press: function () {
        filterItm.setSelected(!filterItm.getSelected());
    }
});

var closeItm = new sap.ui.unified.ShellHeadItem({
    tooltip: "Close",
    icon: sap.ui.core.IconPool.getIconURI("decline"),
    press: function () {
        oShell.setShowCurtain(false);
        setState(oldState);
    }
});

var logoffItm = new sap.ui.unified.ShellHeadItem({
    tooltip: "Logoff",
    icon: sap.ui.core.IconPool.getIconURI("log"),
    press: function () {
    }
});


var oDialog = new sap.ui.commons.Dialog({
    width: "200px",
    height: "200px",
    title: "Dialog"
});

var oCntnt = {
//    "SEARCH": {
//        curt: [
//            new CurtainContent("CurtainContent", {
//                text: "Title",
//                content: [
//                    new sap.ui.commons.Button({
//                        text: "Focussable Button #1",
//                        press: function () {
//                            alert("Yay, you pressed a button...");
//                        }
//                    }),
//                    new sap.ui.commons.TextView({text: "This is the search screen\n\n" + sLorem})
//                ]
//            })
//        ],
//        curtPane: [
//            new sap.ui.commons.Button({
//                text: "Focussable Pane Button #1",
//                press: function () {
//                    alert("Yay, you pressed a button...");
//                }
//            }),
//            new sap.ui.commons.TextView({text: "Configure the search screen ...\n\n" + sLorem})
//        ],
//        items: [curtainConfigItm, homeItm],
//        rightItems: [filterItm, closeItm, logoffItm]
//    },
        "HOME": {
            pane: [
                new sap.m.Page({
//                    title: "Title",
                    showNavButton: false,
                    subHeader: new sap.m.Bar({
                        contentMiddle: [
                            new sap.m.Button({
                                text: "history",
                                type: sap.m.ButtonType.Transparent,
                                width: "100%"
                            })
                        ]
                    })
                })
            ],
            cntnt: [
//                new sap.ui.commons.Button({
//                    text: "Click to open App 1",
//                    press: function () {
//                        setState("APP1");
//                    }
//                }),
//                new sap.m.Button("super", {text: "bouton"}),
//                new sap.ui.commons.Button({
//                    text: "Click to open App 2",
//                    press: function () {
//                        setState("APP2");
//                    }
//                }),
//                new sap.ui.commons.Button({
//                    text: "Open a dialog",
//                    press: function () {
//                        oDialog.open();
//                    }
//                }),
//            new sap.m.CustomTile({
//                content: util.UiFactory.createDescription('Tile based entry pages are a modern design pattern allowing the user to quickly get an overview, understand the system status and navigate to the places where action is required', 'OnlyTop')
//            }),
                new sap.m.Page({
                    title: "Easy Pognon",
                    content: [
                        new sap.m.ObjectHeader({
                            title: "David",
                            number: "100",
                            numberUnit: "BTC",
                            attributes: [
                                new sap.m.ObjectAttribute({
                                    text: "mail"
                                }),
                                new sap.m.ObjectAttribute({
                                    text: "www.sap.com",
                                    active: true,
                                    press: function () {
                                        sap.m.URLHelper.redirect("http://www.sap.com", true);
                                    }
                                })
                            ]
                        })
                    ]
                })
            ],
            items: [configItm],
            rightItems: [logoffItm]
        },
        "APP1": {
            cntnt: [new sap.ui.commons.TextView({text: "This is App 1\n\n" + sLorem})],
            items: [homeItm],
            rightItems: [logoffItm]
        },
        "APP2": {
            cntnt: [new sap.ui.commons.TextView({text: "This is App 2\n\n" + sLorem})],
            items: [homeItm],
            rightItems: [logoffItm]
        }
    }
    ;

function applyContent() {
    var cntnt = oCntnt[state];

    if (cntnt.pane) {
        oShell.removeAllPaneContent();
        for (var i = 0; i < cntnt.pane.length; i++) {
            oShell.addPaneContent(cntnt.pane[i]);
        }
    }
//    if (cntnt.curtPane) {
//        oShell.removeAllCurtainPaneContent();
//        for (var i = 0; i < cntnt.curtPane.length; i++) {
//            oShell.addCurtainPaneContent(cntnt.curtPane[i]);
//        }
//    }
//    if (cntnt.curt) {
//        oShell.removeAllCurtainContent();
//        for (var i = 0; i < cntnt.curt.length; i++) {
//            oShell.addCurtainContent(cntnt.curt[i]);
//        }
//    }
    if (cntnt.cntnt) {
        oShell.removeAllContent();
        for (var i = 0; i < cntnt.cntnt.length; i++) {
            oShell.addContent(cntnt.cntnt[i]);
        }
    }
    if (cntnt.items) {
        oShell.removeAllHeadItems();
        for (var i = 0; i < cntnt.items.length; i++) {
            oShell.addHeadItem(cntnt.items[i]);
        }
    }
    if (cntnt.rightItems) {
        oShell.removeAllHeadEndItems();
        for (var i = 0; i < cntnt.rightItems.length; i++) {
            oShell.addHeadEndItem(cntnt.rightItems[i]);
        }
    }
}
;

function setState(sState) {
    switch (sState) {
//        case "SEARCH":
//            oShell.setShowCurtain(true);
//            oldState = state;
//            break;
        case "APP1":
        case "APP2":
            oShell.setShowCurtain(false);
            oShell.setShowPane(false);
            oShell.setHeaderHiding(true);
            break;
        case "HOME":
        default:
            sState = "HOME";
            oShell.setHeaderHiding(false);
            oShell.setShowCurtain(false);
            break;
    }
    configItm.setSelected(false);
    filterItm.setSelected(false);

    state = sState;
    applyContent();
}
;

var oShell = new sap.ui.unified.Shell({
//    icon: jQuery.sap.getModulePath("sap.ui.core", '/') + "mimes/logo/sap_73x36.gif",
    icon: "images/easypognon73x36.jpg",
    headerHiding: false,
    showPane: true
//    search: new SearchFieldPlaceHolder("sf", {
//        search: function (oEvent) {
//            if (state != "SEARCH") {
//                setState("SEARCH");
//            }
//        }
//    })
});
oShell.placeAt("content");

setState("HOME");


</script>

</body>

</html>