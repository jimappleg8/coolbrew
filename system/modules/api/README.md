# APIs and rTags for Coolbrew

Originally, Coolbrew was a tag-based system that could integrate easily with websites developed and hosted within our internal networks. Before long, however, vendors who were developing in their own environments needed access to some of the key tools that we insisted they use for things like providing store searches and contacting our consumer services department.

## rTags

Remote Tags or rTags are a cross between a React component and an API. You call them via an HTTP request, and they return formatted HTML rather than raw data. rTags have the advantage of being available outside our network, and they were easy to implement. To make things even easier, I built Wordpress plugins that implemented the rTag calls and displayed the results.

You can find code for rTags within some of the modules (at `controllers/v1/`), like FAQs, Mailform, and Stores.

## The APIs

Over time, Coolbrew had to evolve to handle new technologies and the demands of third-party vendors who didn't want to use the unfamiliar tags and rTags that were available. The next logical step was to create an API to make the data available with no strings attached.

APIs were less common back then, and even they were often ignored by vendors building sites for us. Nonetheless, the APIs were available and worked well. I created some Wordpress plugins and Drupal extensions to integrate API data into these popular CMSs.