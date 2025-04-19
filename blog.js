/**
 * Backsure Global Support - Blog Module
 * This script handles fetching and displaying blog posts from the backend
 */

// Global state for blog posts
let blogState = {
  currentPage: 1,
  totalPages: 1,
  activeCategory: 'all',
  searchTerm: '',
  postsPerPage: 6,
  isLoading: false
};

document.addEventListener('DOMContentLoaded', function() {
  // Initialize the blog functionality after the DOM is fully loaded
  initBlog();
});

/**
 * Initialize the blog functionality
 */
function initBlog() {
  // Load posts from API when page loads
  fetchBlogPosts();
  
  // Setup event listeners
  setupCategoryFilters();
  setupSearch();
  setupPagination();
  
  // Setup scroll to top for pagination clicks
  document.querySelector('.blog-pagination').addEventListener('click', function(e) {
    if (e.target.classList.contains('page-link') || e.target.parentElement.classList.contains('page-link')) {
      // Smooth scroll to the top of the blog container
      document.querySelector('.blog-container').scrollIntoView({ behavior: 'smooth' });
    }
  });
}

/**
 * Set up category filter buttons
 */
function setupCategoryFilters() {
  const filterBtns = document.querySelectorAll('.filter-btn');
  
  filterBtns.forEach(btn => {
    btn.addEventListener('click', function() {
      // Don't do anything if this category is already active
      if (this.classList.contains('active')) return;
      
      // Update active button styles
      filterBtns.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      
      // Update state
      blogState.activeCategory = this.getAttribute('data-category');
      blogState.currentPage = 1; // Reset to first page when changing filters
      
      // Show loading state
      showLoadingState();
      
      // Fetch posts with the new filter
      fetchBlogPosts();
    });
  });
}

/**
 * Set up search functionality
 */
function setupSearch() {
  const searchInput = document.getElementById('blog-search-input');
  const searchBtn = document.getElementById('blog-search-btn');
  
  // Search button click
  searchBtn.addEventListener('click', function() {
    performSearch();
  });
  
  // Enter key in search input
  searchInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      performSearch();
    }
  });
  
  function performSearch() {
    const newSearchTerm = searchInput.value.trim();
    
    // Only search if term has changed
    if (newSearchTerm !== blogState.searchTerm) {
      blogState.searchTerm = newSearchTerm;
      blogState.currentPage = 1; // Reset to first page when searching
      
      // Show loading state
      showLoadingState();
      
      // Fetch posts with search term
      fetchBlogPosts();
    }
  }
}

/**
 * Set up pagination event handlers
 * (The actual pagination links are created dynamically)
 */
function setupPagination() {
  // This is a delegation approach since pagination links are created dynamically
  document.querySelector('.blog-pagination').addEventListener('click', function(e) {
    e.preventDefault();
    
    // Find the clicked link (could be the i tag inside)
    let target = e.target;
    if (!target.classList.contains('page-link')) {
      target = target.closest('.page-link');
    }
    
    if (!target) return; // No pagination link clicked
    
    // Don't do anything for the current active page or ellipsis
    if (target.classList.contains('active') || target.textContent === '...') return;
    
    // Determine the page to navigate to
    let newPage = blogState.currentPage;
    
    if (target.classList.contains('prev')) {
      newPage--;
    } else if (target.classList.contains('next')) {
      newPage++;
    } else {
      // It's a numbered link
      newPage = parseInt(target.textContent);
    }
    
    // Don't proceed if the page is invalid
    if (newPage < 1 || newPage > blogState.totalPages) return;
    
    // Update state and fetch
    blogState.currentPage = newPage;
    
    // Show loading state
    showLoadingState();
    
    // Fetch posts for the new page
    fetchBlogPosts();
  });
}

/**
 * Show loading state while fetching posts
 */
function showLoadingState() {
  if (blogState.isLoading) return; // Already loading
  
  blogState.isLoading = true;
  
  const blogGrid = document.getElementById('blog-grid');
  
  // Reduce opacity of existing content
  blogGrid.style.opacity = '0.5';
  blogGrid.style.pointerEvents = 'none';
  
  // Add a loading spinner
  const loadingSpinner = document.createElement('div');
  loadingSpinner.className = 'loading-spinner';
  loadingSpinner.innerHTML = `
    <div class="spinner-container">
      <div class="spinner"></div>
      <p>Loading posts...</p>
    </div>
  `;
  
  // Add spinner to DOM
  document.querySelector('.blog-container .container').appendChild(loadingSpinner);
  
  // Add loading spinner styles if not already in the document
  if (!document.getElementById('loading-spinner-styles')) {
    const style = document.createElement('style');
    style.id = 'loading-spinner-styles';
    style.textContent = `
      .loading-spinner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        text-align: center;
      }
      
      .spinner-container {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }
      
      .spinner {
        width: 40px;
        height: 40px;
        margin: 0 auto 10px;
        border: 4px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top: 4px solid var(--primary-color);
        animation: spin 1s linear infinite;
      }
      
      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }
    `;
    document.head.appendChild(style);
  }
}

/**
 * Hide loading state after posts are fetched
 */
function hideLoadingState() {
  blogState.isLoading = false;
  
  const blogGrid = document.getElementById('blog-grid');
  blogGrid.style.opacity = '1';
  blogGrid.style.pointerEvents = 'auto';
  
  // Remove loading spinner
  const loadingSpinner = document.querySelector('.loading-spinner');
  if (loadingSpinner) {
    loadingSpinner.remove();
  }
}

/**
 * Fetch blog posts from the API
 */
function fetchBlogPosts() {
  // Create URL with query parameters
  const apiUrl = new URL('/api/blog-posts', window.location.origin);
  apiUrl.searchParams.append('page', blogState.currentPage);
  apiUrl.searchParams.append('limit', blogState.postsPerPage);
  
  if (blogState.activeCategory !== 'all') {
    apiUrl.searchParams.append('category', blogState.activeCategory);
  }
  
  if (blogState.searchTerm) {
    apiUrl.searchParams.append('search', blogState.searchTerm);
  }
  
  // AJAX request to the API
  fetch(apiUrl)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      // Hide loading state
      hideLoadingState();
      
      // Update state
      blogState.totalPages = data.meta.totalPages || 1;
      
      // Render the posts and pagination
      renderPosts(data.posts);
      renderPagination();
      
      // Display no results message if needed
      if (data.posts.length === 0) {
        showNoResultsMessage();
      }
    })
    .catch(error => {
      console.error('Error fetching blog posts:', error);
      hideLoadingState();
      
      // For demo/development, use dummy data when API fails
      if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        console.log('Using dummy data for development');
        const dummyData = getDummyPosts();
        renderPosts(dummyData.posts);
        blogState.totalPages = dummyData.meta.totalPages;
        renderPagination();
      } else {
        // Show error message in production
        showErrorMessage();
      }
    });
}

/**
 * Render blog posts to the DOM
 */
function renderPosts(posts) {
  const blogGrid = document.getElementById('blog-grid');
  
  // Clear existing posts
  blogGrid.innerHTML = '';
  
  // Add posts to the grid
  posts.forEach((post, index) => {
    const postElement = createPostElement(post, index === 0 && blogState.currentPage === 1);
    blogGrid.appendChild(postElement);
  });
  
  // Add animation classes with delay for each post
  const postElements = blogGrid.querySelectorAll('.blog-card');
  postElements.forEach((post, index) => {
    setTimeout(() => {
      post.classList.add('fadeIn');
    }, index * 100);
  });
  
  // Add fadeIn animation styles if not already in the document
  if (!document.getElementById('post-animation-styles')) {
    const style = document.createElement('style');
    style.id = 'post-animation-styles';
    style.textContent = `
      .blog-card {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease;
      }
      
      .blog-card.fadeIn {
        opacity: 1;
        transform: translateY(0);
      }
    `;
    document.head.appendChild(style);
  }
}

/**
 * Create a blog post element
 */
function createPostElement(post, isFeatured = false) {
  const article = document.createElement('article');
  article.className = 'blog-card';
  article.setAttribute('data-category', post.category.slug);
  
  // Add featured class if needed
  if (isFeatured && post.featured) {
    article.classList.add('featured-post');
    
    // Add featured tag
    const featuredTag = document.createElement('span');
    featuredTag.className = 'featured-tag';
    featuredTag.textContent = 'Featured';
    article.appendChild(featuredTag);
  }
  
  // Structure the post
  article.innerHTML = `
    <div class="blog-card-image">
      <img src="${post.image}" alt="${post.title}">
    </div>
    <div class="blog-card-content">
      <h3 class="blog-card-title"><a href="/blog/${post.slug}">${post.title}</a></h3>
      <div class="blog-meta">
        <span class="category">${post.category.name}</span>
        <span class="date"><i class="far fa-calendar-alt"></i> ${formatDate(post.publishedAt)}</span>
      </div>
      <p class="blog-card-excerpt">${post.excerpt}</p>
      <a href="/blog/${post.slug}" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
    </div>
  `;
  
  return article;
}

/**
 * Render pagination links
 */
function renderPagination() {
  const paginationContainer = document.querySelector('.blog-pagination');
  paginationContainer.innerHTML = '';
  
  // Don't show pagination if there's only one page
  if (blogState.totalPages <= 1) return;
  
  // Add previous button if not on first page
  if (blogState.currentPage > 1) {
    const prevLink = document.createElement('a');
    prevLink.href = '#';
    prevLink.className = 'page-link prev';
    prevLink.innerHTML = '<i class="fas fa-chevron-left"></i> Previous';
    paginationContainer.appendChild(prevLink);
  }
  
  // Determine which page links to show
  let startPage = Math.max(1, blogState.currentPage - 1);
  let endPage = Math.min(blogState.totalPages, blogState.currentPage + 1);
  
  // Ensure we show at least 3 pages if available
  if (endPage - startPage < 2) {
    if (startPage === 1) {
      endPage = Math.min(3, blogState.totalPages);
    } else {
      startPage = Math.max(1, blogState.totalPages - 2);
    }
  }
  
  // Always show first page
  if (startPage > 1) {
    const firstPageLink = document.createElement('a');
    firstPageLink.href = '#';
    firstPageLink.className = 'page-link';
    firstPageLink.textContent = '1';
    paginationContainer.appendChild(firstPageLink);
    
    // Add ellipsis if needed
    if (startPage > 2) {
      const ellipsis = document.createElement('span');
      ellipsis.className = 'page-link';
      ellipsis.textContent = '...';
      paginationContainer.appendChild(ellipsis);
    }
  }
  
  // Add page links
  for (let i = startPage; i <= endPage; i++) {
    const pageLink = document.createElement('a');
    pageLink.href = '#';
    pageLink.className = 'page-link';
    if (i === blogState.currentPage) {
      pageLink.classList.add('active');
    }
    pageLink.textContent = i;
    paginationContainer.appendChild(pageLink);
  }
  
  // Always show last page
  if (endPage < blogState.totalPages) {
    // Add ellipsis if needed
    if (endPage < blogState.totalPages - 1) {
      const ellipsis = document.createElement('span');
      ellipsis.className = 'page-link';
      ellipsis.textContent = '...';
      paginationContainer.appendChild(ellipsis);
    }
    
    const lastPageLink = document.createElement('a');
    lastPageLink.href = '#';
    lastPageLink.className = 'page-link';
    lastPageLink.textContent = blogState.totalPages;
    paginationContainer.appendChild(lastPageLink);
  }
  
  // Add next button if not on last page
  if (blogState.currentPage < blogState.totalPages) {
    const nextLink = document.createElement('a');
    nextLink.href = '#';
    nextLink.className = 'page-link next';
    nextLink.innerHTML = 'Next <i class="fas fa-chevron-right"></i>';
    paginationContainer.appendChild(nextLink);
  }
}

/**
 * Show a message when no posts are found
 */
function showNoResultsMessage() {
  const blogGrid = document.getElementById('blog-grid');
  
  const noResults = document.createElement('div');
  noResults.className = 'no-results';
  noResults.innerHTML = `
    <div class="no-results-content">
      <i class="fas fa-search"></i>
      <h3>No blog posts found</h3>
      <p>We couldn't find any posts matching your criteria. Try adjusting your search or filter.</p>
      <button class="reset-filters-btn">Reset Filters</button>
    </div>
  `;
  
  blogGrid.appendChild(noResults);
  
  // Add event listener to reset button
  const resetBtn = noResults.querySelector('.reset-filters-btn');
  resetBtn.addEventListener('click', function() {
    // Reset all filters
    blogState.activeCategory = 'all';
    blogState.searchTerm = '';
    blogState.currentPage = 1;
    
    // Update UI
    document.querySelectorAll('.filter-btn').forEach(btn => {
      btn.classList.remove('active');
      if (btn.getAttribute('data-category') === 'all') {
        btn.classList.add('active');
      }
    });
    
    document.getElementById('blog-search-input').value = '';
    
    // Fetch posts again
    showLoadingState();
    fetchBlogPosts();
  });
  
  // Add no results styles if not already in the document
  if (!document.getElementById('no-results-styles')) {
    const style = document.createElement('style');
    style.id = 'no-results-styles';
    style.textContent = `
      .no-results {
        grid-column: 1 / -1;
        padding: 40px 0;
        text-align: center;
      }
      
      .no-results-content {
        background-color: white;
        border-radius: 8px;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      }
      
      .no-results i {
        font-size: 3rem;
        color: var(--accent-color);
        margin-bottom: 15px;
      }
      
      .no-results h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: var(--primary-color);
      }
      
      .no-results p {
        color: #666;
        margin-bottom: 20px;
      }
      
      .reset-filters-btn {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s;
      }
      
      .reset-filters-btn:hover {
        background-color: var(--primary-dark);
      }
    `;
    document.head.appendChild(style);
  }
}

/**
 * Show an error message when API request fails
 */
function showErrorMessage() {
  const blogGrid = document.getElementById('blog-grid');
  
  const errorMessage = document.createElement('div');
  errorMessage.className = 'api-error';
  errorMessage.innerHTML = `
    <div class="error-content">
      <i class="fas fa-exclamation-circle"></i>
      <h3>Something went wrong</h3>
      <p>We're having trouble loading blog posts right now. Please try again later.</p>
      <button class="retry-btn">Try Again</button>
    </div>
  `;
  
  blogGrid.innerHTML = '';
  blogGrid.appendChild(errorMessage);
  
  // Add event listener to retry button
  const retryBtn = errorMessage.querySelector('.retry-btn');
  retryBtn.addEventListener('click', function() {
    showLoadingState();
    fetchBlogPosts();
  });
  
  // Add error styles if not already in the document
  if (!document.getElementById('error-styles')) {
    const style = document.createElement('style');
    style.id = 'error-styles';
    style.textContent = `
      .api-error {
        grid-column: 1 / -1;
        padding: 40px 0;
        text-align: center;
      }
      
      .error-content {
        background-color: white;
        border-radius: 8px;
        padding: 40px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      }
      
      .api-error i {
        font-size: 3rem;
        color: var(--danger-color, #e74a3b);
        margin-bottom: 15px;
      }
      
      .api-error h3 {
        font-size: 1.5rem;
        margin-bottom: 10px;
        color: var(--primary-color);
      }
      
      .api-error p {
        color: #666;
        margin-bottom: 20px;
      }
      
      .retry-btn {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s;
      }
      
      .retry-btn:hover {
        background-color: var(--primary-dark);
      }
    `;
    document.head.appendChild(style);
  }
}

/**
 * Format a date string
 */
function formatDate(dateString) {
  const date = new Date(dateString);
  const options = { year: 'numeric', month: 'long', day: 'numeric' };
  return date.toLocaleDateString('en-US', options);
}

/**
 * Get dummy posts for development/demo
 */
function getDummyPosts() {
  const posts = [
    {
      id: 1,
      title: '5 Ways Outsourcing Can Accelerate Your Business Growth',
      slug: '5-ways-outsourcing-can-accelerate-business-growth',
      excerpt: 'Learn how strategic outsourcing can help you scale faster, reduce costs, and focus on your core business strengths in today\'s competitive landscape.',
      image: 'images/blog/blog-featured.jpg',
      featured: true,
      publishedAt: '2025-04-15T12:00:00Z',
      category: { name: 'Outsourcing Tips', slug: 'outsourcing' }
    },
    {
      id: 2,
      title: 'Building Effective Remote Teams: Best Practices',
      slug: 'building-effective-remote-teams',
      excerpt: 'Discover proven strategies for managing remote teams effectively and maintaining strong team culture across borders.',
      image: 'images/blog/blog-1.jpg',
      featured: false,
      publishedAt: '2025-04-12T10:30:00Z',
      category: { name: 'HR Management', slug: 'hr-management' }
    },
    {
      id: 3,
      title: 'Streamlining Financial Operations: Key Strategies for SMEs',
      slug: 'streamlining-financial-operations',
      excerpt: 'Learn practical approaches to optimize your financial processes, reduce costs, and improve financial visibility for better decision-making.',
      image: 'images/blog/blog-2.jpg',
      featured: false,
      publishedAt: '2025-04-08T09:15:00Z',
      category: { name: 'Finance & Accounting', slug: 'finance' }
    },
    {
      id: 4,
      title: 'Understanding UAE Corporate Tax: A Guide for Businesses',
      slug: 'understanding-uae-corporate-tax',
      excerpt: 'Navigate the complexities of UAE\'s corporate tax system with this comprehensive guide for business owners and finance teams.',
      image: 'images/blog/blog-3.jpg',
      featured: false,
      publishedAt: '2025-04-05T14:45:00Z',
      category: { name: 'Compliance & Admin', slug: 'compliance' }
    },
    {
      id: 5,
      title: 'Digital Transformation: Adapting Your Business for Future Success',
      slug: 'digital-transformation-business-success',
      excerpt: 'Explore the essential steps to successfully implement digital transformation in your business and stay ahead of the competition.',
      image: 'images/blog/blog-4.jpg',
      featured: false,
      publishedAt: '2025-03-30T11:20:00Z',
      category: { name: 'Business Growth', slug: 'business-growth' }
    },
    {
      id: 6,
      title: 'How to Choose the Right Outsourcing Partner for Your Business',
      slug: 'choose-right-outsourcing-partner',
      excerpt: 'Understand the key factors to consider when selecting an outsourcing partner that aligns with your business goals and values.',
      image: 'images/blog/blog-5.jpg',
      featured: false,
      publishedAt: '2025-03-25T13:10:00Z',
      category: { name: 'Outsourcing Tips', slug: 'outsourcing' }
    }
  ];
  
  // Filter posts based on current filters
  let filteredPosts = [...posts];
  
  // Apply category filter
  if (blogState.activeCategory !== 'all') {
    filteredPosts = filteredPosts.filter(post => post.category.slug === blogState.activeCategory);
  }
  
  // Apply search filter
  if (blogState.searchTerm) {
    const term = blogState.searchTerm.toLowerCase();
    filteredPosts = filteredPosts.filter(post => 
      post.title.toLowerCase().includes(term) || 
      post.excerpt.toLowerCase().includes(term) || 
      post.category.name.toLowerCase().includes(term)
    );
  }
  
  // Calculate pagination
  const totalPosts = filteredPosts.length;
  const totalPages = Math.ceil(totalPosts / blogState.postsPerPage);
  
  // Get posts for current page
  const startIndex = (blogState.currentPage - 1) * blogState.postsPerPage;
  const endIndex = startIndex + blogState.postsPerPage;
  const paginatedPosts = filteredPosts.slice(startIndex, endIndex);
  
  return {
    posts: paginatedPosts,
    meta: {
      currentPage: blogState.currentPage,
      totalPages: totalPages,
      totalPosts: totalPosts
    }
  };
}
