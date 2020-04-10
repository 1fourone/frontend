/* 
    toolkit.js
    ----
    Responsible for handling all toolkit item information
*/


function createSelectionList(name, isMultiple=false, options=[]) {
    var wrapper = document.createElement("div");
    wrapper.setAttribute("class", "list-select");

    var select = document.createElement("select");
    select.setAttribute("id", "ls-" + name);
    select.multiple = isMultiple;

    var def = document.createElement("option");
    def.setAttribute("value", "");
    def.innerHTML = "Select a " + name;
    select.appendChild(def);

    options.forEach(opt => {
        var tmp = document.createElement("option");
        tmp.setAttribute("value", opt);
        tmp.innerHTML = opt;
        select.appendChild(tmp);
    });

    wrapper.appendChild(select);
    var referenceNode = document.getElementById("placeholder");
    referenceNode.parentNode.insertBefore(wrapper, referenceNode.nextSibling);
    /* access the selected value(s) via <elem>.value if single
    or Array.prototype.map.call(el.selectedOptions, function(x){ return x.value }); */
}

function createSearchBox(name) {
    var el = document.createElement("textarea");
    el.setAttribute("id", "sb-" + name);
    el.setAttribute("onkeyup", "test()");
    var referenceNode = document.getElementById("placeholder");
    referenceNode.parentNode.insertBefore(el, referenceNode.nextSibling);
    /* access the word list on search box by <elem>.value.split(" ") */
}

function createSubmissionButton(text, source) {
    var el = document.createElement("button");
    el.setAttribute("id", "submit");
    el.setAttribute("onclick", "submit('" + source + "');");
    el.innerHTML = text;
    
    var referenceNode = document.getElementById("placeholder");
    referenceNode.parentNode.insertBefore(el, referenceNode.nextSibling);
    /* each specific page is responsible for implementing a submit() function
    for their specific uses! */
}

function createPanel(name, content=[], labels=[]) {
    var el = document.createElement("div");
    el.setAttribute("class", "panel");
    el.setAttribute("id", "panel-" + name);

    for(let i=0; i < content.length; i++) {
        var tmp = createPanelItem(name, i, content[i], labels[i]);
        el.appendChild(tmp);
    }
    document.getElementById("placeholder").appendChild(el);
}

function createPanelItem(name, index, content, label) {
    var wrapper = document.createElement("div");
    wrapper.setAttribute("class", "panel-item");
    wrapper.setAttribute("id", "pi-" + name + "-" + index);

    wrapper.addEventListener("click", (e) => {
        var items = document.getElementsByClassName("panel-item");
        console.log(items);
        for(let i=0; i < items.length; i++) {
            if(items[i].id == e.target.parentNode.id)
                items[i].style = "background: #eeaaee;";
            else
                items[i].style = "background: none;";
        }
        onPanelItemClicked(e.target.parentNode.id);
        /* each page is responsible for implementing onPanelItemClicked */
    }, false);

    var img = document.createElement("img");
    img.setAttribute("src", "../images/" + content);
    wrapper.appendChild(img);

    var lb = document.createElement("label");
    lb.innerHTML = label;
    wrapper.appendChild(lb);

    return wrapper;
}

/* TODO: next/previous items */

function test() {
    /*
    var selections = document.getElementsByTagName("select");
    for(let i=0; i < selections.length; i++) {
        console.log(selections[i].value);
    }
    var sb = document.getElementById("sb-test");
    console.log("The word contents of the search box are: " + sb.value.split(" "));
    */
}