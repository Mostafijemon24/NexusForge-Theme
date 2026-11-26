=== FusionWrite AI ===
Contributors: mostafijemon
Tags: ai writer, openrouter, gemini, openai, blog generator
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 2.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Write blog posts with AI from a focused, full-page workspace. Works with OpenRouter, Gemini, Groq, OpenAI and other compatible APIs, free ones included.

== Description ==

FusionWrite AI turns a topic into a finished WordPress draft: title, structured text, a featured image and images inside the post.

It opens as a full-page workspace with its own menu on the left. Each task has its own page, so nothing is crammed onto one screen.

**Works with free and paid AI**

* **OpenRouter** - one key for hundreds of models. Leave the model on automatic and it uses whichever free model is available.
* **Google Gemini** - Google AI Studio keys include a free tier.
* **Groq** - fast open models with a free plan.
* **OpenAI** - GPT models, billed by OpenAI.
* **Custom API** - any OpenAI-compatible endpoint, such as Mistral, Cerebras or a local Ollama server.

Model names change often. FusionWrite reads the list of models from the provider and picks a current one for you, or lets you choose from the list.

**Pages of the workspace**

* **Overview** - setup steps, connection status and recent posts.
* **Write** - one post: topic, provider, language, tone, length, images and optional custom instructions. The finished post opens in an editor on the same page.
* **Bulk** - a list of topics, written one after another with live progress.
* **History** - every post the plugin wrote, with the model that wrote it.
* **Providers** - API keys and models, with a connection test for each provider.
* **Images** - the service that makes images, with a sample button.
* **Preferences** - the language, tone, length, post status and category new posts start with.
* **Guide** - how to get a free key and what to do when something fails.

**Images**

* **Pollinations** - free, no key needed (one image every 45 seconds, with a small logo).
* **OpenRouter** - any image model available with your OpenRouter key.
* **OpenAI** - GPT Image models.
* **Stability AI** - Stable Image Core.

Each image inside a post is made for the section it sits above.

**Guided tour**

A step-by-step tour walks through every page the first time the workspace is opened, and can be taken again from the menu.

== External services ==

This plugin sends data to third-party services only when you ask it to write a post, make an image, test a connection or load a list of models, and only to the service you have chosen and set up. Nothing is sent when the plugin is merely installed.

1. **OpenRouter** (text and, optionally, images)
   - Data sent: your API key, the writing or image prompt (which contains your topic and options) and the chosen model name.
   - [Terms](https://openrouter.ai/terms) - [Privacy](https://openrouter.ai/privacy)

2. **Google Gemini API** (text)
   - Data sent: your API key, the writing prompt and the chosen model name.
   - [Terms](https://ai.google.dev/gemini-api/terms) - [Privacy](https://policies.google.com/privacy)

3. **Groq** (text)
   - Data sent: your API key, the writing prompt and the chosen model name.
   - [Terms](https://groq.com/terms-of-use/) - [Privacy](https://groq.com/privacy-policy/)

4. **OpenAI** (text and, optionally, images)
   - Data sent: your API key, the writing or image prompt and the chosen model name.
   - [Terms](https://openai.com/policies/terms-of-use) - [Privacy](https://openai.com/policies/privacy-policy)

5. **Pollinations** (images, when chosen as the image service)
   - Data sent: the image prompt, which contains the post title or a section heading. No key and no account are involved; the service sees the IP address of your web server.
   - [Terms](https://pollinations.ai/terms) - [Privacy](https://pollinations.ai/privacy)

6. **Stability AI** (images, when chosen as the image service)
   - Data sent: your API key and the image prompt.
   - [Terms](https://stability.ai/terms-of-use) - [Privacy](https://stability.ai/privacy-policy)

7. **Custom API** (text, only if you enter a base URL)
   - Data sent: the API key you entered, the writing prompt and the model name, to the address you entered. The Providers page offers the addresses of Mistral (https://api.mistral.ai), Cerebras (https://api.cerebras.ai) and a local Ollama server as shortcuts; their own terms apply if you use them.

== Installation ==

1. Upload the `fusionwrite-ai` folder to `/wp-content/plugins/`, or install the ZIP under Plugins > Add New > Upload Plugin.
2. Activate the plugin. The workspace opens and offers a short tour.
3. Open **Providers**, paste an API key and choose **Save and test**.
4. Open **Write**, enter a topic and choose **Write post**.

== Frequently Asked Questions ==

= Can I use it without paying for an API? =

Yes. OpenRouter, Google Gemini and Groq have free plans, and Pollinations makes images without a key. Free plans limit how many requests you can make per minute and per day.

= Which OpenRouter model is used? =

With the model field empty, requests go to OpenRouter's free models router, which picks an available free model. To choose one yourself, use **Load models** on the Providers page.

= Where are my API keys stored? =

In your site's database, in the plugin's settings. They are shown masked in the workspace and are sent only to the provider they belong to.

= I updated from version 1.0. Do I need to enter my keys again? =

No. OpenAI, Gemini and Stability AI keys are carried over the first time version 2 runs.

= How do I get the WordPress menu back? =

Use **Back to WordPress** in the menu, or turn off the full-page option under **Preferences** to keep the WordPress menu and toolbar visible.

== Screenshots ==

1. Overview - setup steps and connection status.
2. Write - a topic and the options for one post.
3. Review - the finished post in the editor.
4. Bulk - a queue of topics with live progress.
5. Providers - keys, models and connection tests.
6. Guided tour.

== Changelog ==

= 2.0.0 =
* New full-page workspace with a menu on the left and a separate page for each task.
* Added OpenRouter, Groq and custom OpenAI-compatible providers, with free models supported.
* Models are read from the provider instead of being fixed in the plugin.
* Added Pollinations (free) and OpenRouter as image services; OpenAI images now use the GPT Image models because DALL-E 3 was retired.
* Text and images are created in separate steps with live progress, which avoids hosting time limits.
* Images inside a post are matched to the section they sit above.
* Added a guided tour, a guide page, a history of written posts and preferences for new posts.
* The editor on the Write page now saves changes to the post.
* API keys are masked and are no longer placed in page source or request addresses.

= 1.0 =
* Initial release.

== Credits ==

The workspace uses the Instrument Sans typeface, bundled with the plugin under the SIL Open Font License 1.1 (see `assets/fonts/OFL.txt`).
