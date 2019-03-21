/*
 * Composites imports.
 */
// Composites/ConfigurationWizard imports.
import { default as OnboardingWizard, MessageBox, LoadingIndicator } from "@yoast/configuration-wizard";
import { sendRequest, decodeHTML } from "@yoast/helpers";

// Import colors from the style guide.
import { colors } from "@yoast/components/style-guide";

// Composites/AngoliaSearch imports.
import AlgoliaSearcher from "@yoast/algolia-search";
// Composites/Plugin imports.
import { default as ScoreAssessments } from "./composites/Plugin/Shared/components/ScoreAssessments";
import { default as Collapsible } from "./composites/Plugin/Shared/components/Collapsible";
import { default as ButtonSection } from "./composites/Plugin/Shared/components/ButtonSection";
import { default as LanguageNotice } from "./composites/Plugin/Shared/components/LanguageNotice";
import { YoastButton } from "./composites/Plugin/Shared/components/YoastButton";
import { default as YoastModal } from "./composites/Plugin/Shared/components/YoastModal";
import { UpsellButton } from "./composites/Plugin/Shared/components/UpsellButton";
import { UpsellLinkButton } from "./composites/Plugin/Shared/components/UpsellLinkButton";
import { default as ContentAnalysis } from "./composites/Plugin/ContentAnalysis/components/ContentAnalysis";
import { default as HelpCenter } from "./composites/Plugin/HelpCenter/HelpCenter.js";
import CornerstoneToggle from "./composites/Plugin/CornerstoneContent/components/CornerstoneToggle";

// Composites/LinkSuggestions imports.
import { default as LinkSuggestions } from "./composites/LinkSuggestions/LinkSuggestions";
// Composites/KeywordSuggestions imports.
import { default as KeywordSuggestions } from "./composites/KeywordSuggestions/KeywordSuggestions";

import { Loader, SvgIcon, YoastSeoIcon } from "@yoast/components";
import { getDirectionalStyle } from "@yoast/helpers";

const getRtlStyle = getDirectionalStyle;

// Composites/CoursesOverview imports
import { default as Card, FullHeightCard } from "./composites/CoursesOverview/Card";
import { default as CardBanner } from "./composites/CoursesOverview/CardBanner";
import { default as CardDetails } from "./composites/CoursesOverview/CardDetails";

export {
	OnboardingWizard,
	HelpCenter,
	MessageBox,
	LinkSuggestions,
	KeywordSuggestions,
	LanguageNotice,
	ContentAnalysis,
	Collapsible,
	ButtonSection,
	LoadingIndicator,
	ScoreAssessments,
	YoastButton,
	YoastModal,
	Loader,
	CornerstoneToggle,
	sendRequest,
	decodeHTML,
	UpsellButton,
	UpsellLinkButton,
	Card,
	FullHeightCard,
	CardBanner,
	CardDetails,
	SvgIcon,
	getRtlStyle,
	AlgoliaSearcher,
	colors,
	YoastSeoIcon,
};

export { default as HelpText } from "./composites/Plugin/Shared/components/HelpText";
export { default as SynonymsInput } from "./composites/Plugin/Shared/components/SynonymsInput";
export * from "./forms";
export * from "./composites/Plugin/ContentAnalysis";
export * from "@yoast/search-metadata-previews";
export { default as utils } from "./utils";
export { localize } from "./utils/i18n";
export { setTranslations } from "./utils/i18n";
export { translate } from "./utils/i18n";
export * from "./composites/Plugin/DashboardWidget";
export { replacementVariablesShape, recommendedReplacementVariablesShape } from "@yoast/search-metadata-previews/SnippetEditor/constants";
export { default as analysis } from "./composites/Plugin/ContentAnalysis/reducers/contentAnalysisReducer";
export { default as WordpressFeed } from "./composites/Plugin/DashboardWidget/components/WordpressFeed";
export { default as SeoAssessment } from "./composites/Plugin/DashboardWidget/components/SeoAssessment";
export { default as VideoTutorial } from "./composites/HelpCenter/views/VideoTutorial";
export { default as KeywordInput } from "./composites/Plugin/Shared/components/KeywordInput";
export { default as Icon } from "./composites/Plugin/Shared/components/Icon";
export { default as YoastWarning } from "./composites/Plugin/Shared/components/YoastWarning";
export { insightsReducer } from "./redux/reducers/insights";
export { setProminentWords } from "./redux/actions/insights";
export { setReadabilityResults,
	setSeoResultsForKeyword,
	setOverallReadabilityScore,
	setOverallSeoScore } from "./composites/Plugin/ContentAnalysis/actions/contentAnalysis";
