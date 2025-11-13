// externalPosts.js
import { useState, useEffect } from "@wordpress/element";

/**
 * Fetch post types from an external WordPress site.
 * @param {string} baseUrl - Base URL of the external site (without trailing slash).
 */
export function useExternalPostTypes(baseUrl) {
	const [postTypes, setPostTypes] = useState([]);
	const [isLoading, setIsLoading] = useState(true);
	const [error, setError] = useState(null);

	useEffect(() => {
		const fetchPostTypes = async () => {
			try {
				const response = await fetch(`${baseUrl}/wp-json/wp/v2/types`);
				if (!response.ok) {
					throw new Error(`Failed to fetch post types: ${response.status}`);
				}
				const data = await response.json();
				const postTypeList = Object.entries(data).map(([slug, details]) => ({
					slug,
					name: details.name,
					rest_base: details.rest_base,
				}));
				setPostTypes(postTypeList);
			} catch (err) {
				setError(err.message);
			} finally {
				setIsLoading(false);
			}
		};

		fetchPostTypes();
	}, [baseUrl]);

	return { postTypes, isLoading, error };
}

/**
 * Fetch posts of a specific type from an external site.
 * @param {string} baseUrl - Base URL of the external site (without trailing slash).
 * @param {string} postType - The slug or rest_base of the post type.
 * @param {object} queryArgs - Optional query args (e.g., { per_page: 10 }).
 */
export function useExternalPosts(baseUrl, postType, queryArgs = {}) {
	const [posts, setPosts] = useState([]);
	const [isLoading, setIsLoading] = useState(true);
	const [error, setError] = useState(null);

	useEffect(() => {
		if (!postType) return;

		const query = new URLSearchParams(queryArgs).toString();
		const fetchPosts = async () => {
			try {
				const response = await fetch(
					`${baseUrl}/wp-json/wp/v2/${postType}?${query}`
				);
				if (!response.ok) {
					throw new Error(`Failed to fetch posts: ${response.status}`);
				}
				const data = await response.json();
				setPosts(data);
			} catch (err) {
				setError(err.message);
			} finally {
				setIsLoading(false);
			}
		};

		fetchPosts();
	}, [baseUrl, postType, JSON.stringify(queryArgs)]);

	return { posts, isLoading, error };
}
