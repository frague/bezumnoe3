import {utils} from './utils';

/*
    Validation of controls against rules given.
*/

class ValidatorsCollection {
  constructor() {
    super();
    this.Clear();
  }

  Init(summary_control, summary_text) {
    this.Summary = $(getElement(summary_control))[0];
    this.SummaryText = summary_text ? "<h2>" + summary_text + "</h2>" : "";
    this.InitSummary();

    for (var id in this.Base) {
      if (id && this.Base[id].Init) {
        this.Base[id].Init();
      }
    }
  }

  InitSummary() {
    if (this.Summary) {
      this.Summary.innerHTML = this.SummaryText;
      doHide(this.Summary);
    }
  }

  ShowSummary(errors) {
    if (this.Summary && errors && errors.length) {
      this.Summary.innerHTML = this.SummaryText + "<li> " + errors.join("<li> ");
      doShow(this.Summary);
    }
  }

  AreValid() {
    this.InitSummary();

    var result = true;
    for (var id in this.Base) {
      if (id && this.Base[id].Validate) {
        if (!this.Base[id].Validate(this.Summary)) {
          result = false;
        }
      }
    }
    return result;
  }
}

var PageValidators = new ValidatorsCollection();

function ValueHasChanged() {
  return PageValidators.AreValid();
};


/* --------------- Single Validator --------------- */

class Validator {
  constructor(control, rule, message, summarize, on_the_fly) {
    this.Control = $(getElement(control))[0];
    this.Rule = rule;
    this.Message = message;
    this.ShowInSummary = summarize;
    this.OnTheFly = on_the_fly;

    this.Id = utils.random(1000, true);
    this.Enabled = true;
  }

  Init() {
    if (this.OnTheFly) {
      this.Control.onchange = ValueHasChanged;
    }

    this.ErrorContainer = document.createElement("div");
    if (!this.ShowInSummary) {
      this.Display(false);
      this.ErrorContainer.innerHTML = this.Message;

      insertAfter(this.ErrorContainer, this.Control);
    }
  }

  Validate(summary_control) {
    if (this.Control && this.Rule.Check(this.Control.value, this.Control)) {
      this.Display(false);
      return true;
    }
    this.Control.focus();
    this.Display(true, summary_control);
    return false;
  }

  Display(state, summary_control) {
    if (summary_control && this.ShowInSummary) {
      summary_control.innerHTML += "<li>" + this.Message;
      doShow(summary_control);
    } else {
      this.ErrorContainer.className = "Validator" + (state ? "" : " Hidden");
    }
  }
}

/* -------------------- Validation Rules -------------------- */
// Required Field

class RequiredField {
  Check(value) {
    return (value.length > 0);
  }
}

// Field Length

class LengthRange {
  constructor(min_length, max_length) {
    this.MinLength = min_length;
    this.MaxLength = max_length;
  }

  Check(value) {
    var l = value.length;
    return (l >= this.MinLength && l <= this.MaxLength);
  }
}

// Equal To

class EqualTo {
  constructor(control) {
    this.Control = control;
  }

  Check(value) {
    return (this.Control && this.Control.value == value);
  }
}

// Match the pattern
var emailPattern = new RegExp("^[0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\.\`\{\|\}\~]+\@[0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\`\{\|\}\~]{2,50}([\.][0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\`\{\|\}\~]{2,50})+$");

class MatchPattern {
  constructor(pattern) {
    this.Pattern = pattern;
  }

  Check(value) {
    return value.match(this.Pattern);
  }
}

// Is Checked
class IsChecked {
  Check(x, control) {
    return control.checked;
  }
}
