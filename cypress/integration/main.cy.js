describe('main page', () => {
  it('visits main page', () => {
    cy.visit('/') // baseUrl
    cy.get('svg.NuxtLogo').should('exist')
  })
})