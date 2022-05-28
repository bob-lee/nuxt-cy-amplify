// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

import { mount } from "@cypress/vue";
import Vue from 'vue';
import Vuetify from 'vuetify';

Vue.use(Vuetify);

Cypress.Commands.add("mount", (MountedComponent, options) => {
  // get the element that our mounted component will be injected into
  const root = document.getElementById("__cy_root");

  // add the v-application class that allows Vuetify styles to work
  if (!root.classList.contains("v-application")) {
    root.classList.add("v-application");
  }

  // add the data-attribute — Vuetify selector used for popup elements to attach to the DOM
  root.setAttribute('data-app', 'true');

  // root.setAttribute('style', 'display: block');

  return mount(MountedComponent, {
    vuetify: new Vuetify({}),
    ...options,
  });
});