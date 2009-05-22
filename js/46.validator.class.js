//2.5
/*
	Validation of controls against rules given.
*/

var PageValidators = new Collection();

PageValidators.Init = function(summary_control, summary_text) {
	this.Summary = summary_control;
	this.SummaryText = summary_text ? summary_text : "";
	this.InitSummary();

	for (var id in this.Base) {
		if (id && this.Base[id].Init) {
			this.Base[id].Init();
		}
	}
};

PageValidators.InitSummary = function() {
	if (this.Summary) {
		this.Summary.innerHTML = this.SummaryText;
		this.Summary.className = "Hidden";
	}
};

PageValidators.AreValid = function() {
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
};

function ValueHasChanged() {
	return PageValidators.AreValid();
};


/* --------------- Single Validator --------------- */

function Validator(control, rule, message, summarize, on_the_fly) {
	this.Control = control;
	this.Rule = rule;
	this.Message = message;
	this.ShowInSummary = summarize;
	this.OnTheFly = on_the_fly;

	this.Id = Math.round(100 * Math.random());
	this.Enabled = true;
};

Validator.prototype.Init = function() {
	if (this.OnTheFly) {
		this.Control.onchange = ValueHasChanged;
	}

	this.ErrorContainer = d.createElement("div");
	if (!this.ShowInSummary) {
		this.Display(false);
		this.ErrorContainer.innerHTML = this.Message;

		insertAfter(this.ErrorContainer, this.Control);
	}
};

Validator.prototype.Validate = function(summary_control) {
	if (this.Control && this.Rule.Check(this.Control.value, this.Control)) {
		this.Display(false);
		return true;
	}
	this.Control.focus();
	this.Display(true, summary_control);
	return false;
};

Validator.prototype.Display = function(state, summary_control) {
	if (summary_control && this.ShowInSummary) {
		summary_control.innerHTML += "<li>" + this.Message;
		summary_control.className = "";
	} else {
		this.ErrorContainer.className = "Validator" + (state ? "" : " Hidden");
   	}
};

/* -------------------- Validation Rules -------------------- */
// Required Field

function RequiredField() {
};

RequiredField.prototype.Check = function(value) {
	return (value.length > 0);
};


// Field Length

function LengthRange(min_length, max_length) {
	this.MinLength = min_length;
	this.MaxLength = max_length;
};

LengthRange.prototype.Check = function(value) {
	var l = value.length;
	return (l >= this.MinLength && l <= this.MaxLength);
};

// Equal To

function EqualTo(control) {
	this.Control = control;
};

EqualTo.prototype.Check = function(value) {
	return (this.Control && this.Control.value == value);
};

// Match the pattern
var emailPattern = new RegExp("^[0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\.\`\{\|\}\~]+\@[0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\`\{\|\}\~]{2,50}([\.][0-9a-zA-Z\!\#\$\'\*\+\-\/\=\?\^_\`\{\|\}\~]{2,50})+$");

function MatchPattern(pattern) {
	this.Pattern = pattern;
};

MatchPattern.prototype.Check = function(value) {
	return value.match(this.Pattern);
};

// Is Checked
function IsChecked() {
};

IsChecked.prototype.Check = function(x, control) {
	return control.checked;
};

