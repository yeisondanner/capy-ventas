"use strict";
export class ApiAccount {
  constructor(URL = "") {
    this.URL = URL;
    console.log(this.URL);
    
  }

  async get(endpoint, params = {}) {
    try {
      let url = new URL(`${this.URL}/Account/${endpoint}`);
      
      if (Object.keys(params).length > 0) {
        url.search = new URLSearchParams(params).toString();
      }

      const response = await fetch(url, {
        headers: {
          Accept: "application/json",
        },
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      return await response.json();
    } catch (error) {
      console.error("Error al hacer la petición GET:", error);
      throw error;
    }
  }

  async post(endpoint, body) {
    try {
      const response = await fetch(`${this.URL}/Account/${endpoint}`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
        },
        body: JSON.stringify(body),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }

      const data = await response.json();
      return data;
    } catch (error) {
      console.error("Error al hacer la petición POST:", error);
      throw error;
    }
  }
}
