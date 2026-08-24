<x-site.layout :page="$page" :head="$head">

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Blog</p>
    <p class="eyebrow">The blog</p>
    <h1>Real estate insights for the <em>northwest suburbs.</em></h1>
    <p class="lead">Market updates, neighborhood guides, and practical advice from a team that&rsquo;s been selling homes here since 2001.</p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="blog-grid">
      @foreach($posts as $post)
      <a class="blog-card" href="{{ $post['href'] }}">
        <span class="blog-cat">{!! $post['cat'] !!}</span>
        <h3>{!! $post['title'] !!}</h3>
        <p>{!! $post['excerpt'] !!}</p>
        <span class="link-arrow">Read article &rarr;</span>
      </a>
      @endforeach
    </div>
  </div>
</section>

<section class="section--tight section--mist">
  <div class="wrap pg-cta">
    <h2 class="h2">Have a question the blog didn&rsquo;t answer?</h2>
    <p class="lead" style="margin:.8rem auto 0;max-width:52ch">Ask us directly &mdash; local questions are our favorite kind.</p>
    <div class="btns">
      <a class="btn btn--primary" href="/contact">Contact Dawn &amp; Josh</a>
      <a class="btn btn--ghost" href="sms:2246284013">Text Josh: (224) 628-4013</a>
    </div>
  </div>
</section>
</x-site.layout>
