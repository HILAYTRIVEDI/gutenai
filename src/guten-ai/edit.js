/**
 * Edit component for the GutenAI Keywords Block.
 * 
 * This component allows users to generate and select AI-suggested keywords
 * based on the post content. It fetches keywords from the custom REST API
 * endpoint and allows users to manually add or modify them.
 *
 * @package GutenAI
 */

import { __ } from '@wordpress/i18n';
import { RichText, useBlockProps } from '@wordpress/block-editor';
import { useCallback, useMemo, useState } from '@wordpress/element';
import { Button, Spinner, Notice } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useSelect } from '@wordpress/data';

import './editor.scss';

/**
 * Edit function for the Gutenberg block.
 * 
 * @param {Object} props - Component props.
 * @param {Object} props.attributes - Block attributes.
 * @param {Function} props.setAttributes - Function to update block attributes.
 * @returns {JSX.Element} Edit component.
 */
export default function Edit({ attributes, setAttributes })
{
	const { keywords = [], generatedKeywords = [] } = attributes;
	const [isLoading, setIsLoading] = useState(false);
	const [error, setError] = useState(null);
	const [cache, setCache] = useState(false);

	/**
	 * Retrieves the post content from the WordPress editor.
	 * @returns {string} Post content.
	 */
	const postContent = useSelect(
		(select) => select('core/editor')?.getEditedPostContent(),
		[]
	);

	/**
	 * Sanitizes post content by removing unwanted characters and tags.
	 * 
	 * @returns {string} Sanitized post content.
	 */
	const sanitizedContent = useMemo(() =>
	{
		if (!postContent) return '';
		return postContent
			.replace(/&#?[a-z0-9]{2,8};/gi, '')
			.replace(/\b[A-Z][A-Z]+\b/g, '')
			.replace(/<br\s*\/?>(?!\n)/gi, '\n\n')
			.replace(/<\/?.+?>/g, '')
			.trim();
	}, [postContent]);

	/**
	 * Fetches AI-generated keywords from the API.
	 * 
	 * Uses the sanitized post content as input. Updates the block attributes
	 * with the fetched keywords if successful.
	 */
	const fetchKeywords = useCallback(async () =>
	{
		if (!sanitizedContent || isLoading || generatedKeywords.length > 0) return;

		setIsLoading(true);
		setError(null);

		try
		{
			const response = await apiFetch({
				path: `guten-ai/v1/keywords?text=${encodeURIComponent(sanitizedContent)}&cache=${cache}`,
				method: 'GET',
			});

			if (response.success && Array.isArray(response.keywords))
			{
				const uniqueKeywords = [...new Set(response.keywords.map((item) => item.keyword))];
				setAttributes({ generatedKeywords: uniqueKeywords });

				if (uniqueKeywords.length === 0)
				{
					setError(__('No keywords found.', 'gutenai'));
				}
			} else
			{
				setError(__('Please check if the API Key is set or not.', 'gutenai'));
			}
		} catch (err)
		{
			setError(__('Error fetching keywords. Please try again.', 'gutenai'));
		} finally
		{
			setIsLoading(false);
		}
	}, [sanitizedContent, isLoading]);

	/**
	 * Adds a keyword to the selected keywords list.
	 * 
	 * @param {string} keyword - Keyword to be added.
	 */
	const handleSelectKeyword = useCallback(
		(keyword) =>
		{
			if (!keywords.includes(keyword))
			{
				const updatedKeywords = [...keywords, keyword];
				setAttributes({ keywords: updatedKeywords });
			}
		},
		[keywords, setAttributes]
	);

	/**
	 * Updates the selected keywords when the user edits the RichText field.
	 * 
	 * @param {string} value - Comma-separated keywords.
	 */
	const handleRichTextChange = useCallback(
		(value) =>
		{
			const updatedKeywords = [...new Set(
				value.split(',')
					.map((item) => item.trim())
					.filter(Boolean)
			)];
			setAttributes({ keywords: updatedKeywords });
		},
		[setAttributes]
	);

	return (
		<div {...useBlockProps()} className="gutenai-keywords-block">
			{error && (
				<Notice status="error" isDismissible onRemove={() => setError(null)}>
					{error}
				</Notice>
			)}

			<div className="gutenai-keywords-fetch">
				<p className="gutenai-keywords-fetch-title">
					<strong>{__('GutenAI Content Suggestions:', 'gutenai')}</strong>
				</p>
				<Button
					variant="primary"
					onClick={fetchKeywords}
					disabled={isLoading || !sanitizedContent}
					className="gutenai-keywords-fetch-button"
				>
					{__('Generate Keywords from Content', 'gutenai')}
					{isLoading && <Spinner />}
				</Button>
			</div>

			{generatedKeywords.slice(0, 20).length > 0 && (
				<div className="gutenai-keywords-list">
					{generatedKeywords.slice(0, 20).map((keyword) => (
						<Button
							key={keyword}
							variant="secondary"
							onClick={() => handleSelectKeyword(keyword)}
							disabled={keywords.includes(keyword)}
						>
							{keyword}
						</Button>
					))}
				</div>
			)}

			<div className="gutenai-keywords-editor">
				<label htmlFor="gutenai-keywords">
					{__('Selected Keywords:', 'gutenai')}
				</label>
				<RichText
					id="gutenai-keywords"
					tagName="p"
					className="gutenai-keywords-input"
					placeholder={__('Enter or edit keywords...', 'gutenai')}
					value={keywords.join(', ')}
					onChange={handleRichTextChange}
					allowedFormats={[]}
				/>
			</div>
		</div>
	);
}