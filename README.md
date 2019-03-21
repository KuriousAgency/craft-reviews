# Reviews plugin for Craft CMS 3.x

product and store reviews

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1.  Open your terminal and go to your Craft project:

        cd /path/to/project

2.  Then tell Composer to load the plugin:

        composer require reviews/reviews

3.  In the Control Panel, go to Settings → Plugins and click the “Install” button for Reviews.

## Reviews Overview

-Insert text here-

## Configuring Reviews

-Insert text here-

## Using Reviews

```
{% set allReviews = craft.reviews.status(null).all() %}
{% set productReviews = craft.reviews.product(product).all() %}
{% set storeReviews = craft.reviews.productId(':empty:').all() %}

{{ productReviews|averageRating }}

{{ review.verifiedBuyer }}
```

## Reviews Roadmap

Some things to do, and ideas for potential features:

-   Release it

Brought to you by [Kurious Agency](https://kurious.agency)
