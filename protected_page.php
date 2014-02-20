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
    <script>document.title = "Easy Pognon"</script>
    <script src="js/formatter.js"></script>
    <script src="js/functions.js"></script>
</head>


<body class="sapUiBody"><h3
    style="font-size:20px;left:30%;opacity:0.3;position:absolute;bottom:75px;z-index:9999;text-shadow:none;"></h3>

<form>
    <div id="MobileContent"></div>
    <input type="hidden" name="applid" value="ZEASY_POGNON"></form>

<script>

//GLOBALS
var walletModel;
var Shell = sap.m.Shell("Shell", {title: ""});
Shell.placeAt("MobileContent");
var App = new sap.m.App("App", {});
Shell.setApp(App);
var easyTitle = "<?php echo htmlentities($_SESSION['username']); ?> Easy Pognon ";
var main = new sap.m.Page("main", {title: easyTitle});
App.addPage(main);
var barMain = new sap.m.Bar("barMain", {});
main.setFooter(barMain);
var settingsBut = new sap.m.Button("settingsBut", {icon: "sap-icon://settings", press: function (oEvent) {
    App.to("settings", "flip");
}});
barMain.addContentRight(settingsBut);

//***************** Filters main page
var filtersTab = new sap.m.IconTabBar("filtersTab", {selectedKey: "{COIN}", select: function (oEvent) {
    var binding = altList.getBinding("items");
    var selected = oEvent.getParameter("selectedKey");
    if (selected == "ALL") {
        binding.filter([]);
    }
    else {
        var filter = new sap.ui.model.Filter("Coin", "EQ", selected);
        binding.filter([filter]);
    }
}});
main.addContent(filtersTab);
var modelfiltersTab = new sap.ui.model.json.JSONModel();
filtersTab.setModel(modelfiltersTab);
//var filter = new sap.m.IconTabFilter("filter", {icon: "sap-icon://save", key: "{Coin}", text: "{Coin}"});
var filter = new sap.m.IconTabFilter("filter", {icon: "{icon}", key: "{Coin}", text: "{Coin}"});
filtersTab.bindAggregation("items", "/", filter);
//******************** End Filters

//************************** main Altcoin List
var altList = new sap.m.List("altList", { headerText: "Altcoin List"});
main.addContent(altList);
var modelaltList = new sap.ui.model.json.JSONModel();
altList.setModel(modelaltList);
var itemsAlt = new sap.m.ObjectListItem("items", {
    icon: "{icon}",
//    intro: "{Wallet}",
    intro: {
        parts: [
            {path: 'Wallet'}
        ],
        formatter: util.Formatter.wallet
    },
    number: "{Balance}",
    numberUnit: "{Coin}",
    selected: true,
    title: "{Coin}",
    type: "DetailAndActive",
    press: function (oEvent) {
        var context = oEvent.oSource.getBindingContext();
        var value = context.getProperty("Coin");
        getOnlineTransactions(value);
    },
    detailPress: function (oEvent) {
        var context = oEvent.oSource.getBindingContext();
        var index = context.getPath("/").substring(1, 2);
        var line = context.getModel().getData();
        var walletValue = line[index];
        walletModel = new sap.ui.model.json.JSONModel(walletValue);
        walletHeader.setModel(walletModel);
        walletList.setModel(walletModel);
        App.to("walletP");
    }
});

altList.bindAggregation("items", "/", itemsAlt);
//************** End Altcoin List

//***************** User Setting page and list
var settings = new sap.m.Page("settings", {navButtonType: "Back", showHeader: true, showNavButton: true, title: "User Settings", navButtonPress: function (oEvent) {
    App.back("main");
}});
App.addPage(settings);
var userSettings = new sap.m.List("userSettings", { headerText: "User Settings"});
settings.addContent(userSettings);
//*************** End user Settings


//******************************* History Transactions
var transactions = new sap.m.Page("transactions", {navButtonType: "Back", showHeader: true, showNavButton: true, title: "Transaction Details", navButtonPress: function (oEvent) {
    App.back("main");
}});
App.addPage(transactions);
var txList = new sap.m.Table("txList", { headerText: ""});
var modeltxList = new sap.ui.model.json.JSONModel();
txList.setModel(modeltxList);
transactions.addContent(txList);
var wallet = new sap.m.Column("wallet", {header: new sap.m.Label({text: "Wallet"}), mergeDuplicates: true });
txList.addColumn(wallet);
var txId = new sap.m.Column("txId", {header: new sap.m.Label({text: "TxId"})});
txList.addColumn(txId);
//var reqDate = new sap.m.ObjectIdentifier("reqDate", {header: new sap.m.Label({text: "Req.Date"}), state: "{path:'RequestDate', formatter: 'util.Formatter.date'}"});
var reqDate = new sap.m.Column("reqDate", {header: new sap.m.Label({text: "Req.Date"})});
txList.addColumn(reqDate);
var commitDate = new sap.m.Column("commitDate", {header: new sap.m.Label({text: "Com.Date"})});
txList.addColumn(commitDate);
var amount = new sap.m.Column("amount", {header: new sap.m.Label({text: "Amount"})});
txList.addColumn(amount);
var balIn = new sap.m.Column("balIn", {header: new sap.m.Label({text: "Bal.In"})});
txList.addColumn(balIn);
var balOut = new sap.m.Column("balOut", {header: new sap.m.Label({text: "Bal.Out"})});
txList.addColumn(balOut);
var tmStmp = new sap.m.Column("tmStmp", {header: new sap.m.Label({text: "TimeStamp"}), demandPopin: true});
txList.addColumn(tmStmp);
var stat = new sap.m.Column("status", {header: new sap.m.Label({text: "Status"})});
txList.addColumn(status);
var colItem = new sap.m.ColumnListItem("colItem", {});
txList.bindAggregation("items", "/", colItem);
var _wallet = new sap.m.Text("_wallet", {text: "{Wallet}"});
colItem.addCell(_wallet);
var _txId = new sap.m.Text("_txId", {text: "{TransactionId}"});
colItem.addCell(_txId);
var _reqDate = new sap.m.Text("_reqDate", {
    text: {
        parts: [
            {path: 'RequestDate'}
        ],
        formatter: util.Formatter.date
    }
});
colItem.addCell(_reqDate);
var _commitDate = new sap.m.Text("_commitDate",
    {text: {
        parts: [
            {path: 'CommitDate'}
        ],
        formatter: util.Formatter.date
    }});
colItem.addCell(_commitDate);
var _amount = new sap.m.Text("_amount", {text: "{Amount}"});
colItem.addCell(_amount);
var _balIn = new sap.m.Text("_balIn", {text: "{BalanceIn}"});
colItem.addCell(_balIn);
var _balOut = new sap.m.Text("_balOut", {text: "{BalanceOut}"});
colItem.addCell(_balOut);
var _tmStmp = new sap.m.Text("_tmStmp", {text: "{Timestamp}"});
colItem.addCell(_tmStmp);
var _status = new sap.m.Text("_status", {text: "{Status}"});
colItem.addCell(_status);

//************************* Wallet Detail Page
var walletP = new sap.m.Page("walletP", {navButtonType: "Back", showHeader: true, showNavButton: true, title: "Wallet Detail", navButtonPress: function (oEvent) {
    App.back("main");
}});
App.addPage(walletP);
var barWallet = new sap.m.Bar("barWallet", {});
walletP.setFooter(barWallet);
var walletBut = new sap.m.Button("walletBut", {icon: "sap-icon://save", press: function (oEvent) {

    updateWallet();

}});
barWallet.addContentRight(walletBut);
var walletHeader = new sap.m.ObjectHeader("walletName", {
    title: "{/Coin}",
    number: "{/Balance}",
    numberUnit: "{/Coin}",
    icon: "{/icon}",
    iconDensityAware: false
});
walletP.addContent(walletHeader);
var walletList = new sap.m.List("walletList", {
    headerText: "Wallet Address",
    items: [
        new sap.m.InputListItem({
            label: "Wallet Address",
            content: new sap.m.Input({
                placeholder: "Address",
                value: "{/Wallet}",
                type: sap.m.InputType.Text
            })
        }),
        new sap.m.InputListItem({
            label: "Wallet Name",
            content: new sap.m.Input({
                placeholder: "Name",
                value: "{/WalletName}",
                type: sap.m.InputType.Text
            })
        })
    ]
});
walletP.addContent(walletList);

//INIT
setTimeout(function () {

    getOnlinealtList();
}, 1);

//FUNCTIONS

function getOnlinefiltersTab(value) {
    $.ajax({
        type: "POST",
        url: "ws/miners-json.php?user=<?php echo htmlentities($_SESSION['username']); ?>&format=json",
        dataType: "json",
        success: function (data) {
            modelfiltersTab.setData(data.miners);
        }
    });
}

function reloadfiltersTab(value) {
    getOnlinefiltersTab(value);
}

function getOnlinealtList(value) {
    $.ajax({
        type: "POST",
        url: "ws/miners-json.php?user=<?php echo htmlentities($_SESSION['username']); ?>&format=json",
        dataType: "json",
        success: function (data) {
            modelaltList.setData(data[0].miners);
            modelfiltersTab.setData(data[1].filters);
            App.to("main");
        }
    });
}

function reloadaltList(value) {
    getOnlinealtList(value);
}

function getOnlineTransactions(value) {
    $.ajax({
        type: "POST",
        url: "ws/transactions-json.php?user=<?php echo htmlentities($_SESSION['username']); ?>&coin=" + value + "&format=json",
        dataType: "json",
        success: function (data) {
            modeltxList.setData(data.transactions);
            App.to("transactions");
        }
    });
}

function updateWallet(value) {
    $.ajax({
        type: "POST",
        url: "ws/update-wallet.php",
        dataType: "json",
        data: walletModel.getJSON(),
        success: function (data) {
            if (data) {
                getOnlinealtList();
            }
        }
    });
}


</script>

</body>

</html>