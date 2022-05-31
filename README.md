# الواجهة البرمجية لمشروع أَمْلِ Amly
تُعنى ملفات المستودع الحالي بتقديم واجهة برمجية API لمحرك ألعاب **أمْلِ Amly** وذلك للتفاعل مع قاعدة البيانات وتوجيه التدريبات وتعزيز التعليم المشترك.

تطبيق الويب: https://amly.app
مستودع الواجهة: https://github.com/drhootch/amlee

##  إرشادات التثبيت:

يستخدم خادم هذه الواجهة البرمجية:
- PHP 7.4
- قاعدة بيانات MySQL

إضافة إلى المكتبات التالية والمعنية بالعربية والذكاء الاصطناعي:
- khaled.alshamaa/ar-php
- php-ai/php-ml

تثبت من خلال:
```
composer install
```

## أمثلة الواجهة البرمجية API:

أمثلة لاستخدام الواجهة البرمجية من محرك الألعاب المصمم على الواجهة:

#### طلب توليد لعبة عشوائية:
https://amly.app/server.php?getGame

#### طلب اقتراح تدريب مناسب لمستوى معين لمعالجة خطأ محدد (عبر خوارزميات KNN للتوصية بالذكاء الاصطناعي):
https://amly.app/server.php?getGame&level=4&rule=3&gravity=0.8


#### تحديد تدريب معين gameid (ومن 0 إلى 10 ومن الممكن إضافة تدريبات أخرى بسهولة):
https://amly.app/server.php?getGame&gameid=6

#### تحديد لغة الأسئلة (مفيد للمتعلمين من غير الناطقين بالعربية):
https://amly.app/server.php?getGame&lang=en

#### طلب كلمة أو باب معجمي عشوائي مع الصوت:
https://amly.app/dictapi.php?query=FAD

وكذلك الأمر إذا أردنا pattern معين لتدريبه عليه، مثل أن تضم الكلمة همزة متوسطة أو عددا معين الأوصاف ومحدد الغدد من الحروف لمراعاة مستوى المتدرب ونحوها أو تنتهي بتاء مربوطة أو تبدأ بألف مكسورة:

https://amly.app/dictapi.php?query=FAD&pattern=^إ

#### تحديد نوع الكلمة من حيث أقسام الكلام POS:
https://amly.app/dictapi.php?query=FAD&POS=verb


ولدعم المحلل النصي المعتمد على Regex وجب الاستناد إلى بيانات ثانوية لضبط نوع الخطأ وتوجيه المتعلم إليه وهذا لارتباط الإملاء العربي بالصرف والنحو:

#### طلب بيانات صرفية للكلمة:

https://amly.app/dictapi.php?word=ضَرَبْتِ&query=POS

ففي هذا المثال يمكن توجيه المتعلم إلى أن تاء ضربت مفتوحة لأنها مصرفة في الماضي مع ضمير الغائبة. مثال لشكل البيانات الذي سيكون على Regex ruleset:

```
[/ا$/, /ى$/, "تمييز الألف الممدودة عن الألف المقصورة",
 {cases: {
POS:["verb", "3. person", "male", "singular", “imperfect”],
comment: {"ar":
"يكتب فعل %Infinitive بالألف المقصورة، لأن مضارعه %Imperfect بالواو, والقاعدة أن الفعل إذا كان مضارعه واو كتب ألفا ممدودة في الماضي مثل دعا يدعو وبدا يبدو أما إذا كانت ياء كُتبت ألفا مقصورة مثل سقى يسقي وجرى يجري"
}}}]
```

#### استخراج الكلمة معراة من الزوائد lemma:

https://amly.app/dictapi.php?word=فاسمهما&query=lemma

وهذا لصرف أيّ لبس في تصحيح الكلمة، فلمعرفة أن كلمة "فإسمهما" وجبت كتابتها "فاسمهما" بهمزة وصل، وجب استخراج أصلها lemma وهو "اسم" لتحديد أنها من الأسماء العشرة وبذلك توجيه المتعلم لذلك.


#### بيانات التغذية
بيانات KNN الأولية موجودة على مستوى الملف التالي: dataset0.1.csv وهي بيانات للتوجيه الأولي لمعايير اختيار نوع التمرين المناسب، لكن مستقبلا سيتعلم المحرك نجاعة التمارين ويغذي نفسه بنفسه فعندما يحدد محرك اللعبة أن تدريبا كان مفيدا في معالجة خطأ ما يتم إرساله بصيغة HTTP POST إلى logger.php وهو ما سيقوم بإضافته إلى بيانات تغذية KNN:
```
{'level':4, 'rule':5, 'gravity':0.8, 'gameid':3}
```



# amlee-serverdemo
The following code source for the server side is used as an API to interact with database and generate games based on an audio/textual dictionary.

Audio files can be browsed on https://amly.app/audio

Frontend implementation of this project can be found on https://github.com/drhootch/amlee

The server side uses:
- PHP 7.4
- MySQL DB

It required the following libs for Arabic & IA:
- khaled.alshamaa/ar-php
- php-ai/php-ml

## Install instruction :
```
composer install
```

## Examples:

Following are some examples of the provided API for the frontend:

#### Generate a random game or a suggestion based on the KNN IA:

https://amly.app/server.php?getGame

#### Or a suggestion based on the KNN IA:
https://amly.app/server.php?getGame&level=4&rule=3&gravity=0.8

#### Specify a game id:
https://amly.app/server.php?getGame&gameid=6

#### Ask for a random entry from the dictionary which includes text/audio and helpful information to teach the learner about the word he's listening to:
https://amly.app/dictapi.php?query=FAD

#### Ask for a random word with specific POS:
https://amly.app/dictapi.php?query=FAD&POS=verb


To boost the regex ruleset and messaging when correcting dictation, certain information are required as:

- Getting morphological details for a specific word:
https://amly.app/dictapi.php?word=ضَرَبْتِ&query=POS

So that we could indicate to the learner for example that last ت in ضربت is مفتوحة because it's a verb cojugated with هي in the perfective aspect.

- Getting the lemma of a specific word:
https://amly.app/dictapi.php?word=فاسمهما&query=lemma

Lemma is useful to get rid of certain ambiguities (As in determining that this word فإسمهما is actually اسم one of the ten nouns that should use همزة وصل).
