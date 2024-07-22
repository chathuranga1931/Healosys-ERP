
class LiveSearch {
    constructor(_myname, _search_input, _search_element_id, _livesearch_api, _suggestion_css_element, _callback) {
        this.suggestionCssElement = _suggestion_css_element;
        this.searchInput = _search_input;
        this.searchElementId = _search_element_id;
        this.livesearch_api = _livesearch_api;
        this.callback = _callback;
        this.currentFocus = -1;
        this.previousValue = "";   
        this.onClickCallBack = this.selectSuggestion_OnClick;
        this.myName = _myname;
    }  
    
    showResult(str) {
        const searchElementId = this.searchElementId;
        if (str.length === 0) {
            document.getElementById(searchElementId).innerHTML = "";
            document.getElementById(searchElementId).style.border = "0px";
            this.previousValue = "";
            return;
        }
        if (str === this.previousValue) {
            return;
        }
        this.previousValue = str;

        const xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = () => {
            if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                document.getElementById(searchElementId).innerHTML = xmlhttp.responseText;
                document.getElementById(searchElementId).style.border = "1px solid #A5ACB2";
                this.currentFocus = -1;
            }
        }

        const livesearch_api = this.livesearch_api;
        const url = `${livesearch_api}?q=${str}&oc=${this.myName}.selectSuggestion_OnClick`;
        // const url = `${livesearch_api}?q=${str}`;
        xmlhttp.open("GET", url, true);
        xmlhttp.send();
    }

    selectSuggestion_OnClick(value) {
        
        const searchElementId = this.searchElementId;
        const searchInput = this.searchInput;

        document.getElementById(searchElementId).innerHTML = "";
        document.getElementById(searchElementId).style.border = "0px";
        this.previousValue = value;
        if(this.callback != null) {
            this.callback(value);
        }
    }

    navigateSuggestions(e) {
        const suggestionBox = document.getElementById(this.searchElementId);
        const items = suggestionBox.getElementsByClassName(this.suggestionCssElement);
        if (e.keyCode === 40) {
            this.currentFocus++;
            this.addActive(items);
        } else if (e.keyCode === 38) {
            this.currentFocus--;
            this.addActive(items);
        } else if (e.keyCode === 13) {
            e.preventDefault();
            if (this.currentFocus > -1 && items.length > 0) {
                items[this.currentFocus].click();
            }
        }
    }

    addActive(items) {
        if (!items) return false;
        this.removeActive(items);
        if (this.currentFocus >= items.length) this.currentFocus = 0;
        if (this.currentFocus < 0) this.currentFocus = items.length - 1;
        items[this.currentFocus].classList.add("selected");
    }

    removeActive(items) {
        for (let i = 0; i < items.length; i++) {
            items[i].classList.remove("selected");
        }
    }

}