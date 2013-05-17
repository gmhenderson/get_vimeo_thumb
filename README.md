Get Vimeo Thumb
===============

A simple ExpressionEngine Plugin to get the URL of a Vimeo video thumbnail. Usage:

	{exp:get_vimeo_thumb:pair id="vimeo_id" size="small|medium|large" refresh="7"}

		{vimeo_thumb_url}

	{/exp:get_vimeo_thumb}

The above would return something like: http://b.vimeocdn.com/ts/437/269/437269672_200.jpg

id = [int]
-------------
The Vimeo video id.

size = ['small' | 'medium' | 'large']
---------------------------
The thumbnail size you wish to get.

refresh = [int]
--------------
The number of days to cache the XML data retrieved from Vimeo.
