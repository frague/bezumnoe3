/*
  Paged view of the long content.
*/

class Pager {
  constructor(holder, callback, per_page, total, current) {
    this.Holder = holder;
    this.Callback = callback;
    this.PerPage = per_page ? per_page : 20;
    this.Total = total;
    this.Current = current ? current : 0;

    this.VisiblePages = 10;
    this.Holder.className += " Pager";
  }

  AddLink(i, prefix, postfix) {
    if (prefix) {
      this.Holder.appendChild(d.createTextNode(prefix));
    }

    var page;
    if (i == this.Current) {
      page = document.createElement("span");
    } else {
      page = document.createElement("a");
      page.href = voidLink;
      page.Pager = this;
      page.onclick = function(){this.Pager.SwitchTo(this)};
    }
    page.innerHTML = i + 1;
    this.Holder.appendChild(page);

    if (postfix) {
      this.Holder.appendChild(d.createTextNode(postfix));
    }
  }

  Offset() {
    return this.Current * this.PerPage;
  }

  Pages() {
    return Math.ceil(this.Total / this.PerPage);
  }

  Print() {
    var pages = this.Pages();

    this.Holder.innerHTML = "";
    var from = this.Current - Math.floor(this.VisiblePages / 2);
    if (from < 0 ) {
      from = 0;
    }
    var till = from + this.VisiblePages;
    if (till > pages) {
      from = pages - this.VisiblePages;
      till = pages;
      if (from < 0 ) {
        from = 0;
      }
    }

    if (from > 0) {
      this.AddLink(0, "", "..");
    }
    for (var i = from; i < till; i++) {
      this.AddLink(i);
    }
        
    if (till < pages) {
      this.AddLink(pages - 1, "..");
    }
  }

  SwitchToPage(num) {
    this.Current = num;
    this.Print();
    if (this.Callback) {
      this.Callback(this.Current);
    }
  }

  SwitchTo(a) {
    this.SwitchToPage((1 * a.innerHTML) - 1);
  }
}

function SwitchPage(el) {
  if (el && el.obj && el.obj.Pager) {
    el.obj.Pager.SwitchToPage(0);
  }
};