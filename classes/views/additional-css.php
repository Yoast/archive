nav.amp-wp-title-bar {
background: <?php echo $this->options['header-color']; ?>;
}
.amp-wp-title, h2, h3, h4 {
color: <?php echo $this->options['headings-color']; ?>;
}
.amp-wp-content {
color: <?php echo $this->options['text-color']; ?>;
}
.amp-wp-content blockquote {
background-color: <?php echo $this->options['blockquote-bg-color']; ?>;
border-color: <?php echo $this->options['blockquote-border-color']; ?>;
color: <?php echo $this->options['blockquote-text-color']; ?>;
}
a, a:active, a:visited {
color: <?php echo $this->options['link-color']; ?>;
text-decoration: <?php echo ( $this->options['underline'] ) ? 'none' : 'underline'; ?>
}
a:hover {
color: <?php echo $this->options['link-color-hover']; ?>;
}
.amp-wp-meta li, .amp-wp-meta li a {
color: <?php echo $this->options['meta-color']; ?>;
}
td, th {
text-align: left;
}