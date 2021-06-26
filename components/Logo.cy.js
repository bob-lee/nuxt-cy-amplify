import { mount } from '@cypress/vue'
import Logo from './Logo.vue'

describe('Logo', () => {
  it('renders a logo', () => {
    const msg = 'Hello Cypress Component Testing!'
    mount(Logo)

    cy.get('svg.NuxtLogoWrong').should('exist')
  })
})