"use strict";

export class ApiBoxmanagement {
  constructor(URL = "") {
    this.URL = URL;
  }

  get(endpoint, params = {}) {
    let url = new URL(`${this.URL}/pos/Boxmanagement/${endpoint}`);
    if (Object.keys(params).length > 0) {
      url.search = new URLSearchParams(params).toString();
    }
    return fetch(url, { headers: { Accept: "application/json" } }).then(
      (res) => {
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
      }
    );
  }

  post(endpoint, formData) {
    return fetch(`${this.URL}/pos/Boxmanagement/${endpoint}`, {
      method: "POST",
      headers: { Accept: "application/json" },
      body: formData,
    }).then((res) => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    });
  }

  delete(endpoint, bodyObj) {
    return fetch(`${this.URL}/pos/Boxmanagement/${endpoint}`, {
      method: "DELETE",
      headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: JSON.stringify(bodyObj),
    }).then((res) => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    });
  }
}
