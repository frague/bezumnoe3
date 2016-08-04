import {settings} from './settings';

var nicknames = new Collection();
var nicknames1;
var max_names = 5;
var name_length = 20;

class Nickname {
  constructor(index, id, name) {
    this.Index = index;
    this.Id = id;
    this.OldName = name;
    this.Name = name;
    this.Mode = 'show';
  }

  IsEmpty() {
    return this.Name == '';
  }

  HasChanged() {
    return this.OldName != this.Name;
  }

  CreateButton(src, action) {
    var button = document.createElement('input');
    button.type = 'image';
    button.RelatedItem = this;
    eval('button.onclick = function(){' + action + '}');
    button.className = 'Button';
    button.style.width = '15px';
    button.style.height = '15px';
    button.src = settings.imagesPath + src;
    return button;
  }

  CreateViewControls() {
    this.Div.innerHTML = '';
    if (this.Mode == 'show') {
      this.Div.innerHTML += (this.Name ? this.Name + (this.Name == me.Login ? '&nbsp;(ваш логин)' : '') : '&lt;не задано&gt;') + '&nbsp;';
      if (this.Id) {
        this.Div.appendChild(this.CreateButton('edit_icon.gif', 'Edit(this)'));
        if (this.Name) {
        this.Div.appendChild(this.CreateButton('delete_icon.gif', 'Clear(this)'));
      }
      }
    } else {
      this.Input = document.createElement('input');
      this.Input.className = 'NewNick';
      this.Input.value = this.Name;
      this.Input.setAttribute('maxlength', name_length);
      this.Div.appendChild(this.Input);
      if (this.Id) {
        this.Div.appendChild(this.CreateButton('icons/done.gif', 'StopEditing(true)'));
        this.Div.appendChild(this.CreateButton('icons/cancel.gif', 'StopEditing(false)'));
      }
    }
  }

  ToString(holder) {
    if (!this.Li) {
      this.Li = document.createElement('li');
    } else {
      this.Li.innerHTML = '';
    }
    this.Radio = createRadio('nickname', ((!me.Nickname && this.Name == me.Login) || (me.Nickname && this.Name == me.Nickname)));
    this.Radio.RelatedItem = this;
    eval('this.Radio.onclick = function(){Select(this)}');

    this.Li.appendChild(this.Radio);
    this.Div = document.createElement('span');

    this.CreateViewControls();

    this.Li.appendChild(this.Div);
    holder.appendChild(this.Li);
  }

  Gather(index) {
    var s = new ParamsBuilder()
      .add('id' + index, this.Id > 0 ? this.Id : '')
      .add('name' + index, this.Name);
    if (this.Radio.checked) s.add('selected', index);
    return s.build();
  }
}

/* Change Nickname class */

class ChangeNickname {
  CreateControls(container) {
    this.Holder = document.createElement('ul');
    this.Holder.className = 'NamesList';

    this.Holder.innerHTML = loadingIndicator;

    container.appendChild(this.Holder);

    this.Status = document.createElement('div');
    this.Status.className = 'Status';
    container.appendChild(this.Status);
    nicknames1 = this;
  }

  requestData() {
    $.get settings.servicesPath + 'nickname.service.php')
      .then(NamesResponse);
  }
}

function NamesResponse(responseText) {
  if (nicknames1.Holder) {

    nicknames.Clear();
    nicknames.Add(new Nickname(0, 0, me.Login));

    try {
      eval(responseText);
    } catch (e) {
    }
    for (var i = nicknames.Count(); i <= max_names; i++) {
      nicknames.Add(new Nickname(i + 1, - (i + 1), ''));
    }
    if (NewNickname != '-1') {
      me.Nickname = NewNickname;
      if (PrintRooms) {
        PrintRooms();
      }
    }
    nicknames1.Holder.innerHTML = '';
    nicknames.ToString(nicknames1.Holder);
  }
};

var activeItem;
function Select(e) {
  if (e.RelatedItem) {
    StopEditing(true);
    var item = e.RelatedItem;
    if (item.IsEmpty()) {
      Edit(e);
    }
  }
};

function Edit(e) {
  if (e.RelatedItem) {
    StopEditing(true);
    var item = e.RelatedItem;
    item.Mode = 'edit';
    item.CreateViewControls();
    item.Input.focus();
    activeItem = item;
  }
};

function Clear(e) {
  if (e.RelatedItem) {
    var item = e.RelatedItem;
    item.Name = '';
    item.CreateViewControls();
  }
};

function StopEditing(acceptChanges) {
  if (activeItem) {
    activeItem.Mode = 'show';
    if (acceptChanges) {
      activeItem.Name = activeItem.Input.value;
    }
    activeItem.CreateViewControls();
  }
};


var nicknameSaving = 0;
function SaveNicknameChanges() {
  if (nicknameSaving) {
    return;
  }
  StopEditing(true);
  nicknameSaving = 1;
  setTimeout('UnLockSaving()', 10000);
  $.post settings.servicesPath + 'nickname.service.php', nicknames.Gather())
    .done(SavingResults);
};

function UnLockSaving() {
  nicknameSaving = 0;
};

function SavingResults(req) {
  UnLockSaving();
  status = '';
  NamesResponse(req);
  if (!status) {
    SetStatus('Изменения сохранены.');
    setTimeout('co.Hide()', 2000);
  }
  ForcePing();
};

var status;
function SetStatus(text) {
  nicknames1.Status.innerHTML = text;
  status = text;
};
