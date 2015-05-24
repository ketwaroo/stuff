The Witcher 3 - Wild Hunt; Bundle file repacker.
================================================

Added on 2015-05-24.

EXPERIMENTAL; USE AT YOUR OWN RISK. Make backups of everything you modify.

Since the preload version of the game was avaible for download about a week before the game was fully available, there's been a MexScript (`.bms` extension) avalible to extract the `.bundle` and `.cache` files used by the game.

The awesome utility, [Quickbms](http://aluigi.altervista.org/quickbms.htm), which runs those `.bms files` has limited reimport capability.

Limited being the operative word. Any file you edit MUST be the same file size as the the one extracted. Even then. some of files in the `.bundle` file have compression applied and quickbms does not always recompress the files back in the same way and you're back to having mismatching file sizes and your games don't work.

This is a day 2 script to repack bundle files to rebuild the bundle files from scratch instead of reimporting.
None of the files are compressed when repacking to avoid any possible corruption. A consequence of this is that the final bundle files are often much larger than the original.



Bu why do we even need this, you ask? Isn't this cheating? And to you, I say, fuck off.


# Requirements
 * The Witcher 3 game. I use the gog.com version.
 * quickms and the witcher3.bms script. See: http://aluigi.altervista.org/quickbms.htm for both.
 * PHP binaries. PHP 5.6 recommended. May be safe to use PHP 5.4.
  * PHP install dir should be added to your path for convenience.
 * Quite a bit of free disk space. Untested with the large bundle files.
 
# Usage

```
 php bundle-repack.php <bundle file> <directory to import.>
```

 * Make backups as necessary.
 * Extract target bundle file using quickbms.
  * Extract each file to a different folder. i.e `content/content0/bundles/xml.bundle` to `extracted/xml` directory.
 * Edit files.
  * It should be obvious that you should not delete/rename/move any of the extracted files.
 * Drop the `bundle-repack.php` file in the game installation folder.
 * Run the command `php bundle-repack.php content/content0/bundles/xml.bundle extracted/xml`.
  * That should normally repack all the files in the selected directory in the target file.
 * DELETE THE `content/metadata.store` FILE. The game uses that file as a cache. Deleting it will force the game to use the newly edited file.
 
 Of course this may blow up your game. So back up your crap.

# TODO
 * Maybe compile to standalone exe with Phalanger or something.
 * use zip comression in the bundle.
