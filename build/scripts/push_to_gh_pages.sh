start=$(date +%s)

function error_exit
{
	echo -e "\e[01;31m$1\e[00m" 1>&2
	exit 1
}

if [ "$POST_BUILD" == "true" ] && [ "$TRAVIS_PULL_REQUEST" == "false" ]; then
	echo -e "Starting to update gh-pages"
	# copy data we're interested in to other place
	cp -R build/result $HOME/build
	# go to home and setup git
	cd $HOME
	git config --global user.email "travis@travis-ci.org"
	git config --global user.name "Travis"
	#using token clone gh-pages branch
	git clone --quiet https://${GH_TOKEN}@github.com/MODULEWork/Http.git  repo > /dev/null || error_exit "Error cloning the repository";
	#go into diractory and copy data we're interested in to that directory
	cd repo
	git checkout gh-pages
	cp -Rf $HOME/build/* .
	#add, commit and push files
	git add -f .
	git commit -m "Travis build $TRAVIS_BUILD_NUMBER pushed to gh-pages"
	git push  > /dev/null
	echo -e "Pushed to GitHub"
else
	echo "Something went wrong..."
	echo "Additional info:"
	echo "$TRAVIS_PULL_REQUEST"
	echo "$POST_BUILD"
fi

end=$(date +%s)
elapsed=$(( $end - $start ))
minutes=$(( $elapsed / 60 ))
seconds=$(( $elapsed % 60 ))
echo "Post-Build process finished in $minutes minute(s) and $seconds seconds"