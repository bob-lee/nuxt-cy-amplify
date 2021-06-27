describe('main page', () => {
  it('visits main page', () => {
    cy.visit('/') // baseUrl
    cy.get('svg.NuxtLogo').should('exist')
    cy.intercept('https://restcountries.eu/rest/v2/name/*').as('search')
    cy.get('input[data-cy=term]').type('united')
    cy.get('button[data-cy=search]').click()
    cy.wait('@search').its('response.body')
      // .should('not.to.be.null')
      .should((body) => {
        expect(body).not.to.be.null
        expect(body.length).to.be.eq(6)
      })
  })
})