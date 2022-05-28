import MyCard from './MyCard.vue'

describe('MyCard', () => {
  it('renders correctly', () => {
    const msg = 'Hello Cypress Component Testing!'
    cy.mount(MyCard)

    cy.get('button')
      .should('contain', 'Button')
  })
})