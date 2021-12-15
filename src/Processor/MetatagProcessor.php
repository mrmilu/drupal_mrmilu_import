<?php

namespace Drupal\mrmilu_import\Processor;


class MetatagProcessor {

  private $metaTitle;
  private $metaDescription;
  private $keywords;
  private $ogTitle;
  private $ogDescription;
  private $ogType;
  private $ogUrl;
  private $ogSiteName;
  private $ogImage;
  private $twitterTitle;
  private $twitterCards;
  private $twitterSite;

  private $mediaProcessor;

  public function __construct() {
    $this->metaTitle = null;
    $this->metaDescription = null;
    $this->keywords = null;
    $this->ogTitle = null;
    $this->ogDescription = null;
    $this->ogType = null;
    $this->ogUrl = null;
    $this->ogSiteName = null;
    $this->ogImage = null;
    $this->twitterTitle = null;
    $this->twitterCards = null;
    $this->twitterSite = null;

    $this->mediaProcessor = new MediaProcessor();
  }

  public function setMetaTitle($metaTitle) {
    $this->metaTitle = $metaTitle;
  }

  public function setMetaDescription($metaDescription) {
    $this->metaDescription = $metaDescription;
  }

  public function setKeywords($keywords) {
    $this->keywords = $keywords;
  }

  public function setOgTitle($ogTitle) {
    $this->ogTitle = $ogTitle;
  }

  public function setOgDescription($ogDescription) {
    $this->ogDescription = $ogDescription;
  }

  public function setOgType($ogType) {
    $this->ogType = $ogType;
  }

  public function setOgUrl($ogUrl) {
    $this->ogUrl = $ogUrl;
  }

  public function setOgSiteName($ogSiteName) {
    $this->ogSiteName = $ogSiteName;
  }

  public function setOgImage($ogImage) {
    $this->ogImage = $ogImage;
  }

  public function setOgImageFromDrive($ogImageProperties) {
    $ogImageUrl = $this->mediaProcessor->ogPathFromDrive($ogImageProperties);
    $this->ogImage = $ogImageUrl;
  }

  public function setTwitterTitle($twitterTitle) {
    $this->twitterTitle = $twitterTitle;
  }

  public function setTwitterCards($twitterCards) {
    $this->twitterCards = $twitterCards;
  }

  public function setTwitterSite($twitterSite) {
    $this->twitterSite = $twitterSite;
  }

  public function getMetatags() {
    return serialize([
      'title' => $this->metaTitle,
      'description' => $this->metaDescription,
      'keywords' => $this->keywords,
      'og_description' => $this->ogDescription,
      'og_title' => $this->ogTitle,
      'og_image' => $this->ogImage,
      'og_url' => $this->ogUrl,
      'og_type' => $this->ogType,
      'og_site_name' => $this->ogSiteName,
      'twitter_cards_title' => $this->twitterTitle,
      'twitter_cards_type' => $this->twitterCards,
      'twitter_cards_site' => $this->twitterSite,
    ]);
  }
}
