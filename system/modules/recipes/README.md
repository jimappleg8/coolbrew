# Recipes Module for Coolbrew

As a natural foods company, recipes were common on our branded websites, but they are a particularly challenging type of content. This recipes module was my solution to the following challenges:

### Ingredients

Ingredient lists can be complicated with sections for different parts of the recipe, and they can be tedious to enter into the system. In our case, we were posting recipes that included our products as ingredients, so we wanted to provide links to those products as well.

I implemented an auto-import of ingredients where a plain text list could be copied into a text field and it would automatically pull the lines into separate ingredient records. From there, each item could be tweaked as needed, including linking it up with a prooduct in our products database.

### Recipe Search

We wanted to allow users to search for keywords, by product, and a flexible set of categories. In the initial case, they wanted categories for meal type, prep and cook time, and dietary concern (e.g. gluten free, vegetarian, etc).

For keyword search I implemented stemmed indexing of the title, instructions and ingredients. Stemming is a technique that allows for variations of words to be found in a search. For example, if you searched for "crab" you might see results for crabs, crabmeat, crabapples and so on.

Product search was implemented by generating a list of all products used in any of the recipes so there would always be at least one result, and the user didn't have to choose from all the many products we manufactured.

Flexible categories were implemented by allowing the user to define them in the admin. Categories were then presented too the user much like the product lists were: with a pull-down menu for each category showing a list of subcategories they could choose from.

## Screen Shots

![Recipe search page](/images/recipes-01.jpg)

![Search results page](/images/recipes-01.jpg)

![Sample recipe](/images/recipes-01.jpg)


