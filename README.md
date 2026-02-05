# c3-table

Work done for a code trial performed for a Real Estate listing company.

# Code License:

This code was written by George Stephanis for evaluation by a prospective employer/client. It's not meant to be used for any purpose besides this, and the work performed has not been paid.

# Disclaimer:

This code was written in a specified web IDE without any tooling. There may be minor issues such as whitespace errors that had been overlooked as a consequence. In order to represent it as the result of this effort, I have not gone in and added additional tooling or cleanups since.

# Playground

[Test it out on Playground](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/georgestephanis/c3-table/refs/heads/main/_playground/blueprint.json)

# Project Specifications:

[Trial Assessment: Senior WordPress Developer | Clever Real Estate](https://docs.google.com/document/d/1aE-4DpcYoCH5rwHDfeKSKq7G7Du0Sl7oL-s5VuxZyB4/edit?usp=sharing)

[Trial Assignment: Data Sample](https://docs.google.com/spreadsheets/d/1OgnsmipNwjJ72hX1RQv7N_JnixnL6KqBjhmX8VATuG0/edit?usp=sharing)

# Architecture:

## Data Structure:

When looking at the data structures and sample data for the company comparison table, it seems there are three main directions that we could go: Block, Post Meta, and a Custom Post Type.

The first option would be to store the company data individually in the attributes of the custom block. This would be achievable through Advanced Custom Fields fieldsets attached to the block -- where a repeater field records multiple datasets as a part of the block. While this would work for a single table, for reasons I'll get into later, it seems like this may not be the most advantageous solution for Clever's needs based on my understanding of the sample data provided.

The second way would be to store the data as postmeta of the post or page containing the table. This is probably a bad idea, especially if there is ever a desire to display two different instances of the table in the same post. The same data would be replicated, whereas it may be desirable to have distinct companies in each.

Finally, the solution that I went with was to maintain the list of companies as a custom post type, and then tables could be generated on the fly for different regions, different clusters, top companies, or whatever future directions this goes in.

By maintaining the list of companies separate from the actual block, it seems to keep better with the sample data I've seen, where all the individual companies are based out of one specific region. This leads me to extrapolate that there is a breadth of companies all over the country or perhaps internationally that could be used, and we're just displaying one small subset at the moment. To let us take advantage of the entire data set, it makes the most sense for me to allow that data to be stored separately and then instantiated with the short list of ones we want to display on the current post, and then we can also do programmatic ones based on Clever's partners when a user is willing to geolocate their region, letting us display the most relevant local real estate agents to them. This has a lot more flexibility and lets us leverage the large data sets that we have to provide more relatable content to users -- thereby resulting in better conversion rates.

## Advanced Custom Fields Implementation:

The generated fieldsets and post type from ACF have been deactivated in the UI and instead exported as PHP and included in the custom plugin, `c3-table`.

This is due to my own personal history, having seen too many instances of someone mistakenly clicking into an ACF fieldset and breaking or disabling necessary fields, resulting in broken functionality and support calls. If something doesn't need to be editable, I'd rather just migrate it to be hard-coded for stability.

The generated Post Type also has some custom code displaying a number of the fields in the admin list table -- these columns can be toggled on and off in the "Screen Options" tab at the top -- but it felt like they may be useful in quickly skimming and managing the registry.

## Design:

I tried to largely mimic the existing aesthetic of the Clever brand -- pulling existing colors such as the blue used in the current design and the green of CTA buttons -- but made a few playful flourishes -- subtle background colors, shifts on the CTA buttons, and slight zooming on the agency logos when hovering over their rows.

As an example of Block Styles, I set up two different takes on aesthetics for the block -- a Clean (default) styling and a secondary blue styling. More could naturally (and relatively trivially) be added -- While I'm comfortable extending existing aesthetics, I don't have any ego tied into it and always welcome art direction from those who specialize in such.

### Side Note: Accessibility

The current Clever Blue -- `rgb( 34, 159, 230 )` -- when used as a background color with white text, has a contrast ratio of 2.92:1 -- which is just below the 3.0 for WCAG AA Accessibility for Large Text. I chose to prioritize the current branding and colors rather than attempting a large accessibility audit, but just as a heads up, there may be a few other things worth flagging.

## Used Data:

I believe that I wound up using most of the data but focused on presenting it in a scalable way that translates well on mobile displays.

The table is broken down into three main columns -- "Company", "Details", and "CTA". Details and CTA have a shared header cell (it felt silly to have an extra one there), and the Details header cell disappears on mobile. Company and Details can both be rephrased in the block settings.

- Company: This cell displays the company's link (if present) and either their logo, or their company name if absent -- along with details about their rating (visually as well as a text representation) and a link to more reviews (if present) and listing the total number of reviews.
- Details: This cell displays most of the data semantically in a `definition-list` element. Apart from the cost field, which will always display, all other fields can be hidden in the block settings by unchecking them. This provides more flexibility for prioritizing needs in varied use cases without overwhelming the user, as either a large detailed table or a small focused bit of data. If it's desired to convert them to independent columns, that's totally feasible, maintaining the same toggle control to choose on a per-block basis the data that is desired to be displayed.
- CTA: This link uses the specified priority of displaying a CTA button if the agency has opted in and we can send them leads. For the moment, the button just opens up a modal dialog placeholder, as managing that form submission felt out of scope for this.

## Mobile Implementation:

The mobile breakpoint restyles a number of the aspects of the semantic HTML table -- it uses CSS Grids to stretch the CTA full width and move it down to a subsequent line, after the other two cells, that then render side by side on their own line.

Due to the efficiency of most of the data in the Details column being rendered in the definition list, it wasn't as necessary to prune out cells, thereby avoiding hindering the data offered to mobile users.

# Future Growth:

If this were an actual client project, I'd float the following ballpark areas to iterate on:

- Update the company selection logic. Instead of only searching by company name, do a more in-depth search that also allows for searching by location, industry, license numbers, etc.
- In addition to just location string, include a latitude/longitude. This would enable searching based on proximity using the Haversine formula in queries for nearby agencies.
- Migrate from an ACF block to a custom React block. This would grant a lot more flexibility, including populating the table dynamically via a REST API or GraphQL query, which would also serve to speed up initial page load times by not making WordPress handle the lookups before page rendering. Finally, by switching to an API-driven block, the same company data would enable the population of similar widgets on other sites from a commonly maintained dataset.
- Evaluate how the CTA lead generation form behaves. See if there's value in pre-vetting users via an automated Twilio integration validating phone numbers or the like before passing them to the agencies -- I've built this sort of integration in the past, and it served to increase the revenue significantly per lead.
- Possibly make the table's header row "sticky" if it winds up being used for longer format tables, so that context for the cells isn't lost as the user scrolls. If so, potentially also adding a scroll-snap functionality on each record and deep links to enable sharing?
