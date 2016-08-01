/*
  Service class for authorization pop-up.
*/

class AuthForm {
  constructor() {
    this.Template = "auth_form";
  }

  CreateControls(container) {
    this.Holder = document.createElement("div");
    this.Holder.className = "AuthForm";

    this.Holder.innerHTML = loadingIndicator;

    container.appendChild(this.Holder);
  }

  TemplateLoaded(responseText) {
    KeepRequestedContent(this.Template, responseText);
    this.Holder.innerHTML = responseText;
  }

  RequestData() {
    RequestContent(this);
  }
}