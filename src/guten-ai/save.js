import { useBlockProps } from '@wordpress/block-editor';

/**
 * Save function for the GutenAI Keywords block.
 *
 * @param {Object} props - Component properties.
 * @param {Object} props.attributes - Block attributes.
 * @param {string[]} props.attributes.keywords - Selected keywords.
 *
 * @returns {JSX.Element|null} The saved block content or null if no keywords are set.
 */
export default function save({ attributes })
{
	const { keywords } = attributes;

	// Return nothing if there are no selected keywords.
	if (!keywords?.length)
	{
		return null;
	}

	// Format keywords by prefixing with '#' and removing spaces.
	const formattedKeywords = keywords.map(keyword => `#${keyword.replace(/\s+/g, '')}`).join(' ');

	return (
		<div {...useBlockProps.save()}>
			<p aria-label="Selected keywords">{formattedKeywords}</p>
		</div>
	);
}
