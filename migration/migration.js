var currentStep = 0;
var steps = [];
var stepsUrls = ["1.migration.php", "2.forum.php", "3.journals.php", "4.galleries.php", "5.forum_update.php"];
var descriptions = [
	"Migrate statuses, users data (user records, profiles, settings, nicknames, relations).",
	"Migrating forums (and forum users) and journals records, comments and friends.",
	"Migrating journal records, settings and comments.",
	"Migrate galleries data.",
	"Update forum to add proper records counts and references to users."
];

//--------------- Step class ------------------

function Step() {
	this.Status = -1;
	this.Control = d.createElement("span");
	this.Control.className = "Step";
};

Step.prototype.DrawAt = function(holder) {
	holder.appendChild(this.Control);
};

Step.prototype.InProgress = function() {
	this.Control.className += " InProgress";
};

Step.prototype.Passed = function() {
	this.Control.className += " Passed";
};

Step.prototype.Failed = function() {
	this.Control.className += " Failed";
};

//---------------------------------------------

function Init(button) {
	button.disabled = true;
	for (var i = 0, l = stepsUrls.length; i < l; i++) {
		steps[i] = new Step();
		steps[i].DrawAt($("indicator"));
	}
	MakeStep();
}

function MakeStep() {
	steps[currentStep].InProgress();
	$("description").innerHTML = "<h4>Step " + (currentStep+1) + ":</h4>" + descriptions[currentStep];
	$("steps").src = stepsUrls[currentStep];
};

function Passed() {
	steps[currentStep].Passed();
	if (currentStep < stepsUrls.length - 1) {
		currentStep++;
		MakeStep();
	}
};

function Failed(why) {
	steps[currentStep].Failed();
	if (why) {
		AddError(why, "Error");
	}
};

function AddError(text, className) {
	var l = d.createElement("li");
	l.innerHTML = text;
	l.className = className;
	$("errors").appendChild(l);
};