/**
 * Returns an object with exceptions for the prominent words researcher
 * @returns {Object} The object filled with exception arrays.
 */
const cardinalNumerals = [ "یک", "دو", "سه", "چهار", "پنج", "شش", "هفت", "هشت", "نه", "ده", "یازده", "دوازده", "سیزده",
	"چهارده", "پانزده", "شانزده", "هفده", "هجده", "نوزده", "بیست", "صد", "هزار", "میلیون", "میلیارد" ];

const ordinalNumerals = [ "اول", "اوّل", "دوم", "سوم", "چهارم", "پنجم", "ششم", "هفتم", "هشتم", "نهم", "دهم", "یازدهم", "دوازدهم",
	"سیزدهم", "چهاردهم", "پانزدهم", "شانزدهم", "هفدهم", "هجدهم", "نوزدهم", "بیستم" ];

const personalPronouns = [ "مرا", "من را", "من‌را", "به من", "تو را", "شما را", "شما", "به تو", "به شما",
	"اون رو", "اونو", "به اون", "اون", "او را", "به او", "او", "به ایشان", "ایشان را", "ایشان", "به ایشون", "ایشون رو",
	"ایشون را", "ایشون", "این", "این را", "آن", "به این", "به آن", "آن را", "این رو", "اینو", "ما را", "به ما", "به اونا",
	"آن‌ها", "آنها را", "آن‌ها را", "به آنها", "به آن‌ها", "اونا", "اونارو", "اونا رو", "من", "تو", "ما", "آنها" ];

const demonstrativePronouns = [  ];

const interrogatives = [  ];

const quantifiers = [  ];

const reflexivePronouns = [ "خودم", "خودت", "خودش", "یک نفر خودش", "خودمان", "خودتان", "خودشان" ];

const indefinitePronouns = [  ];

const relativePronouns = [  ];

const prepositions = [ "با", "بی", "در", "را", "یا", "اگر", "مگر", "نه", "چه",
	"باری", "بر", "برای", "برای این", "برای این که", "برای آن که", "برای آن", "از برای", "خواه", "زیرا", "که",
	"نیز", "چون", "چونان که", "چونان‌که", "چنان", "چنان‌چه", "چنانچه", "چنان‌که", "چونکه", "چون که", "چون‌که",
	"چندان که", "چندان‌که", "زیرا که", "زیراکه", "همین که", "همین‌که", "همان که", "همان‌که", "بلکه", "جز", "الا", "الاّ", "الی",
	"تا اینکه", "تااینکه", "تا آنکه", "تاآنکه", "آن‌جا که", "آن‌گاه که", "از آن‌جا که", "ازآنجاکه", "از آن‌که", "ازآنکه", "زیرا",
	"چون‌که", "چون که", "از این رو", "ازاین‌رو", "ازین‌رو", "از بس", "ازبس", "از بس که", "ازبس‌که", "از بهر آن‌که", "اکنون که",
	"اگرچه", "اگر چنانچه", "اگرچنانچه", "الا این‌که", "با این حال", "بااین‌حال", "با این‌که", "بااین‌که", "بااینکه", "با وجود این",
	"باوجوداین", "با این وجود", "بس که", "از بس که", "بس‌که", "از بس‌که", "به شرط آن‌که", "به‌شرط آن‌که", "به شرطی که",
	"به شروطی که", "بعد از", "قبل از", "از بعد از", "از قبل از", "اندر", "بدون", "علیه", "ضد", "غیر",
	"واسه‎ی", "برای", "واسه", "برای" ];

const conjunctions = [  ];

const interviewVerbs = [  ];

const intensifiers = [  ];

const auxiliariesAndDelexicalizedVerbs = [ ];

const generalAdjectivesAdverbs = [ ];

const interjections = [  ];

const recipeWords = [  ];

const timeWords = [ "فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", "مهر", "آبان", "آذر", "دی", "بهمن", "اسفند" ];

const vagueNouns = [  ];

const miscellaneous = [ ];

const transitionWords = [ "دوباره", "قطعاً", "حتماً", "اصلاً", "قاعدتاً", "ظبیعتاً", "شاید", "کاملاً", "به", "از", "و", "همچنین",
	"هم", "مانند", "مثل", "شبیه به", "ولی", "اما", "امّا", "لیکن", "ولو", "در ضمن", "در کنار", "ترجیحاً", "وگرنه", "پس", "سپس",
	"وقتی", "زمانی که", "به خاطر", "مخصوصاً", "مشخصاً", "در کل", "بعد", "قبل", "تا" ];

/**
 * Returns function words for Farsi.
 *
 * @returns {Object} Farsi function words.
 */
export default function() {
	return {
		// These word categories are filtered at the ending of word combinations.
		filteredAtEnding: [].concat( ordinalNumerals, generalAdjectivesAdverbs ),

		// These word categories are filtered at the beginning and ending of word combinations.
		filteredAtBeginningAndEnding: [].concat( prepositions, prepositions, conjunctions,
			demonstrativePronouns, intensifiers, quantifiers ),

		// These word categories are filtered everywhere within word combinations.
		filteredAnywhere: [].concat( transitionWords, personalPronouns,
			reflexivePronouns, interjections, cardinalNumerals, interviewVerbs,
			auxiliariesAndDelexicalizedVerbs, indefinitePronouns, interrogatives, miscellaneous,
			recipeWords, timeWords, vagueNouns ),

		// These categories are used in the passive voice assessment. If they directly precede a participle, the sentence part is not passive.
		cannotDirectlyPrecedePassiveParticiple: [].concat( prepositions, demonstrativePronouns, ordinalNumerals, quantifiers ),

		/*
		These categories are used in the passive voice assessment. If they appear between an auxiliary and a participle,
		the sentence part is not passive.
		*/
		cannotBeBetweenPassiveAuxiliaryAndParticiple: [].concat( interviewVerbs, auxiliariesAndDelexicalizedVerbs ),

		// This export contains all of the above words.
		all: [].concat( prepositions, cardinalNumerals, ordinalNumerals, demonstrativePronouns, reflexivePronouns,
			personalPronouns, quantifiers, indefinitePronouns, interrogatives, prepositions, conjunctions, interviewVerbs,
			transitionWords, intensifiers, auxiliariesAndDelexicalizedVerbs, interjections, generalAdjectivesAdverbs,
			recipeWords, vagueNouns, miscellaneous, relativePronouns ),
	};
}
